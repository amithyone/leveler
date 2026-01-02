<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class MailController extends Controller
{
    private $imapHost = 'localhost';
    private $imapPort = 143;
    private $smtpHost = 'localhost';
    private $smtpPort = 587; // Use port 587 with STARTTLS for encryption
    private $email = 'mail@levelercc.com';
    private $password = '00000000';

    /**
     * Display inbox
     */
    public function index(Request $request)
    {
        if (!function_exists('imap_open')) {
            return view('admin.mail.inbox', [
                'emails' => [],
                'folder' => 'INBOX',
                'page' => 1,
                'totalPages' => 0,
                'totalEmails' => 0,
                'error' => 'PHP IMAP extension is not installed. Please install php-imap package.'
            ]);
        }

        $folder = $request->get('folder', 'INBOX');
        $page = $request->get('page', 1);
        $perPage = 20;
        
        $emails = [];
        $totalEmails = 0;
        $error = null;
        $availableFolders = ['INBOX'];

        try {
            // Connect to IMAP without TLS/SSL (port 143)
            $mailbox = @imap_open(
                "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}",
                $this->email,
                $this->password
            );

            if ($mailbox) {
                // List all folders to find available folders and correct folder name
                $allFolders = imap_list($mailbox, "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}", "*");
                $actualFolderName = $folder;
                
                // Extract available folder names
                foreach ($allFolders as $f) {
                    if (preg_match('/\{[^}]+\}(.+)$/', $f, $matches)) {
                        $folderName = imap_utf7_decode($matches[1]);
                        // Remove dot prefix for display
                        $displayName = ltrim($folderName, '.');
                        if (!in_array($displayName, $availableFolders)) {
                            $availableFolders[] = $displayName;
                        }
                        
                        // Find the actual folder name (handle case sensitivity and dot-prefixed folders)
                        if (strcasecmp($folderName, $folder) === 0 || 
                            strcasecmp($folderName, '.' . $folder) === 0 ||
                            strcasecmp('.' . $folderName, $folder) === 0 ||
                            strcasecmp($displayName, $folder) === 0) {
                            $actualFolderName = $folderName;
                        }
                    }
                }
                
                // Select the folder
                $folderPath = "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}{$actualFolderName}";
                $reopened = @imap_reopen($mailbox, $folderPath);
                
                if (!$reopened) {
                    $error = 'Failed to open folder: ' . imap_last_error();
                    imap_close($mailbox);
                } else {
                    // Get mailbox status
                    $status = imap_status($mailbox, $folderPath, SA_ALL);
                    $totalEmails = $status->messages ?? 0;

                    // Get emails (most recent first)
                    // IMAP message numbers are 1-based and sequential
                    if ($totalEmails > 0) {
                        // Calculate the range correctly
                        $start = max(1, $totalEmails - ($page * $perPage) + 1);
                        $end = max(1, $totalEmails - (($page - 1) * $perPage));
                        
                        // Ensure start <= end
                        if ($start > $end) {
                            $temp = $start;
                            $start = $end;
                            $end = $temp;
                        }
                        
                        // Get message numbers in reverse order (newest first)
                        $messageNumbers = [];
                        for ($i = $end; $i >= $start; $i--) {
                            if ($i >= 1 && $i <= $totalEmails) {
                                $messageNumbers[] = $i;
                            }
                        }
                        
                        foreach ($messageNumbers as $msgNumber) {
                            // Verify message exists before accessing
                            if ($msgNumber < 1 || $msgNumber > $totalEmails) {
                                continue;
                            }
                            
                            $header = @imap_headerinfo($mailbox, $msgNumber);
                            if ($header) {
                                // For Sent folder, show "To" instead of "From"
                                // Check both the requested folder and actual folder name
                                $isSentFolder = strcasecmp($folder, 'Sent') === 0 || 
                                               strcasecmp($actualFolderName, 'Sent') === 0 || 
                                               strcasecmp($actualFolderName, '.Sent') === 0;
                                
                                if ($isSentFolder) {
                                $to = isset($header->to) && count($header->to) > 0 
                                    ? $header->to[0]->mailbox . '@' . $header->to[0]->host 
                                    : 'Unknown';
                                $toName = isset($header->to[0]->personal) 
                                    ? imap_mime_header_decode($header->to[0]->personal)[0]->text 
                                    : '';
                                $emails[] = [
                                    'number' => $msgNumber,
                                    'from' => $to,
                                    'from_name' => $toName ?: $to,
                                    'subject' => isset($header->subject) ? imap_mime_header_decode($header->subject)[0]->text : '(No Subject)',
                                    'date' => $header->date,
                                    'seen' => ($header->Unseen == 'U') ? false : true,
                                    'recent' => ($header->Recent == 'N') ? false : true,
                                ];
                            } else {
                                $emails[] = [
                                    'number' => $msgNumber,
                                    'from' => $header->from[0]->mailbox . '@' . $header->from[0]->host,
                                    'from_name' => isset($header->from[0]->personal) ? imap_mime_header_decode($header->from[0]->personal)[0]->text : '',
                                    'subject' => isset($header->subject) ? imap_mime_header_decode($header->subject)[0]->text : '(No Subject)',
                                    'date' => $header->date,
                                    'seen' => ($header->Unseen == 'U') ? false : true,
                                    'recent' => ($header->Recent == 'N') ? false : true,
                                ];
                            }
                        }
                    }
                }

                    imap_close($mailbox);
                }
            } else {
                $error = 'Failed to connect to mail server: ' . imap_last_error();
            }
        } catch (\Exception $e) {
            $error = 'Error fetching emails: ' . $e->getMessage();
            Log::error('Mail inbox error: ' . $e->getMessage());
        }

        $totalPages = ceil($totalEmails / $perPage);
        
        // Use available folders if we have them, otherwise default
        $folders = !empty($availableFolders) ? $availableFolders : ['INBOX', 'Sent'];

        return view('admin.mail.inbox', compact('emails', 'folder', 'folders', 'page', 'totalPages', 'totalEmails', 'error'));
    }

    /**
     * Show compose form
     */
    public function compose(Request $request)
    {
        $to = $request->get('to', '');
        $subject = $request->get('subject', '');
        $replyTo = $request->get('reply_to', '');
        
        return view('admin.mail.compose', compact('to', 'subject', 'replyTo'));
    }

    /**
     * Send email
     */
    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'cc' => 'nullable|email',
            'bcc' => 'nullable|email',
        ]);

        try {
            // Configure mail settings dynamically
            Config::set('mail.mailers.smtp.host', $this->smtpHost);
            Config::set('mail.mailers.smtp.port', $this->smtpPort);
            Config::set('mail.mailers.smtp.username', $this->email);
            Config::set('mail.mailers.smtp.password', $this->password);
            Config::set('mail.mailers.smtp.encryption', 'tls'); // Enable TLS encryption
            Config::set('mail.from.address', $this->email);
            Config::set('mail.from.name', 'Leveler Mail');

            $to = $request->to;
            $subject = $request->subject;
            $body = $request->body;
            $cc = $request->cc;
            $bcc = $request->bcc;

            // Add email signature with logo to the body
            $signature = view('emails.partials.signature')->render();
            $bodyWithSignature = $body . $signature;
            
            $sentMessage = Mail::send([], [], function ($message) use ($to, $subject, $bodyWithSignature, $cc, $bcc) {
                $message->from($this->email, 'Leveler Mail')
                    ->to($to)
                    ->subject($subject)
                    ->html($bodyWithSignature);
                
                if ($cc) {
                    $message->cc($cc);
                }
                
                if ($bcc) {
                    $message->bcc($bcc);
                }
            });
            
            // Save sent email to Sent folder via IMAP
            try {
                $mailbox = @imap_open(
                    "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}",
                    $this->email,
                    $this->password
                );
                
                if ($mailbox) {
                    // Create Sent folder if it doesn't exist
                    $folders = imap_list($mailbox, "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}", "*");
                    $sentFolderExists = false;
                    $sentFolderName = 'Sent';
                    foreach ($folders as $f) {
                        $decodedFolder = imap_utf7_decode($f);
                        if (stripos($decodedFolder, 'Sent') !== false || stripos($f, 'Sent') !== false) {
                            $sentFolderExists = true;
                            // Extract folder name
                            if (preg_match('/\{[^}]+\}(.+)$/', $f, $matches)) {
                                $sentFolderName = imap_utf7_decode($matches[1]);
                            }
                            break;
                        }
                    }
                    
                    if (!$sentFolderExists) {
                        $sentFolderPath = "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}Sent";
                        @imap_createmailbox($mailbox, imap_utf7_encode($sentFolderPath));
                    }
                    
                    // Get the sent message content and save it to Sent folder
                    if ($sentMessage && $sentMessage->getSymfonySentMessage()) {
                        $originalMessage = $sentMessage->getSymfonySentMessage()->getOriginalMessage();
                        
                        // Convert message to string
                        $messageString = '';
                        foreach ($originalMessage->toIterable() as $chunk) {
                            $messageString .= $chunk;
                        }
                        
                        // Append to Sent folder
                        $sentFolderPath = "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}Sent";
                        @imap_append($mailbox, imap_utf7_encode($sentFolderPath), $messageString);
                    }
                    
                    imap_close($mailbox);
                }
            } catch (\Exception $e) {
                // Log but don't fail the send operation
                Log::warning('Failed to save to Sent folder: ' . $e->getMessage());
            }

            return redirect()->route('admin.mail.index')
                ->with('success', 'Email sent successfully!');
        } catch (\Exception $e) {
            Log::error('Mail send error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * View email
     */
    public function view(Request $request, $id)
    {
        if (!function_exists('imap_open')) {
            return view('admin.mail.view', [
                'email' => null,
                'error' => 'PHP IMAP extension is not installed. Please install php-imap package.'
            ]);
        }

        $email = null;
        $error = null;
        $folder = $request->get('folder', 'INBOX');

        try {
            $mailbox = @imap_open(
                "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}",
                $this->email,
                $this->password
            );

            if ($mailbox) {
                // List all folders to find the correct folder name
                $allFolders = imap_list($mailbox, "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}", "*");
                $actualFolderName = $folder;
                
                // Find the actual folder name (handle case-insensitive and dot-prefixed)
                foreach ($allFolders as $f) {
                    if (preg_match('/\{[^}]+\}(.+)$/', $f, $matches)) {
                        $folderName = imap_utf7_decode($matches[1]);
                        $displayName = ltrim($folderName, '.');
                        if (strcasecmp($folderName, $folder) === 0 || 
                            strcasecmp($folderName, '.' . $folder) === 0 ||
                            strcasecmp($displayName, $folder) === 0) {
                            $actualFolderName = $folderName;
                            break;
                        }
                    }
                }
                
                // Select the correct folder
                $folderPath = "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}{$actualFolderName}";
                @imap_reopen($mailbox, $folderPath);
                
                $header = @imap_headerinfo($mailbox, $id);
                
                if ($header) {
                    $body = imap_body($mailbox, $id);
                    
                    // Try to get HTML body if available
                    $structure = imap_fetchstructure($mailbox, $id);
                    $htmlBody = $this->getMessageBody($mailbox, $id, $structure);
                    
                    // Safely extract email data
                    $fromEmail = '';
                    $fromName = '';
                    if (isset($header->from[0])) {
                        $fromEmail = ($header->from[0]->mailbox ?? '') . '@' . ($header->from[0]->host ?? '');
                        if (isset($header->from[0]->personal)) {
                            $decodedPersonal = @imap_mime_header_decode($header->from[0]->personal);
                            $fromName = $decodedPersonal && isset($decodedPersonal[0]) ? $decodedPersonal[0]->text : '';
                        }
                    }
                    
                    $email = [
                        'number' => $id,
                        'from' => $fromEmail,
                        'from_name' => $fromName,
                        'to' => isset($header->to) ? array_map(function($to) {
                            return ($to->mailbox ?? '') . '@' . ($to->host ?? '');
                        }, is_array($header->to) ? $header->to : [$header->to]) : [],
                        'cc' => isset($header->cc) ? array_map(function($cc) {
                            return ($cc->mailbox ?? '') . '@' . ($cc->host ?? '');
                        }, is_array($header->cc) ? $header->cc : [$header->cc]) : [],
                        'subject' => isset($header->subject) ? (@imap_mime_header_decode($header->subject)[0]->text ?? $header->subject) : '(No Subject)',
                        'date' => $header->date ?? date('r'),
                        'body' => $htmlBody ?: $body,
                        'attachments' => $this->getAttachments($mailbox, $id, $structure),
                    ];

                    // Mark as read
                    imap_setflag_full($mailbox, $id, "\\Seen");
                }

                imap_close($mailbox);
            } else {
                $error = 'Failed to connect to mail server: ' . imap_last_error();
            }
        } catch (\Exception $e) {
            $error = 'Error fetching email: ' . $e->getMessage();
            Log::error('Mail view error: ' . $e->getMessage());
        }

        return view('admin.mail.view', compact('email', 'error', 'folder'));
    }

    /**
     * Delete email
     */
    public function delete(Request $request, $id)
    {
        if (!function_exists('imap_open')) {
            return redirect()->route('admin.mail.index')
                ->with('error', 'PHP IMAP extension is not installed.');
        }

        $folder = $request->get('folder', 'INBOX');

        try {
            $mailbox = @imap_open(
                "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}",
                $this->email,
                $this->password
            );

            if ($mailbox) {
                // List all folders to find the correct folder name
                $allFolders = imap_list($mailbox, "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}", "*");
                $actualFolderName = $folder;
                
                // Find the actual folder name
                foreach ($allFolders as $f) {
                    if (preg_match('/\{[^}]+\}(.+)$/', $f, $matches)) {
                        $folderName = imap_utf7_decode($matches[1]);
                        $displayName = ltrim($folderName, '.');
                        if (strcasecmp($folderName, $folder) === 0 || 
                            strcasecmp($folderName, '.' . $folder) === 0 ||
                            strcasecmp($displayName, $folder) === 0) {
                            $actualFolderName = $folderName;
                            break;
                        }
                    }
                }
                
                // Select the correct folder
                $folderPath = "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}{$actualFolderName}";
                @imap_reopen($mailbox, $folderPath);
                
                imap_delete($mailbox, $id);
                imap_expunge($mailbox);
                imap_close($mailbox);
                
                return redirect()->route('admin.mail.index', ['folder' => $folder])
                    ->with('success', 'Email deleted successfully!');
            } else {
                return redirect()->route('admin.mail.index', ['folder' => $folder])
                    ->with('error', 'Failed to connect to mail server');
            }
        } catch (\Exception $e) {
            Log::error('Mail delete error: ' . $e->getMessage());
            return redirect()->route('admin.mail.index', ['folder' => $folder])
                ->with('error', 'Failed to delete email: ' . $e->getMessage());
        }
    }

    /**
     * Get message body (HTML preferred)
     */
    private function getMessageBody($mailbox, $messageNumber, $structure)
    {
        if (!$structure) {
            return null;
        }

        $body = '';
        
        if ($structure->type == 1) { // Multipart
            foreach ($structure->parts as $partNumber => $part) {
                $partNumber = $partNumber + 1;
                $subStructure = $part;
                
                if ($subStructure->subtype == 'HTML') {
                    $body = imap_fetchbody($mailbox, $messageNumber, $partNumber);
                    if ($subStructure->encoding == 3) {
                        $body = base64_decode($body);
                    } elseif ($subStructure->encoding == 4) {
                        $body = quoted_printable_decode($body);
                    }
                    return $body;
                }
            }
            
            // If no HTML, get text
            foreach ($structure->parts as $partNumber => $part) {
                $partNumber = $partNumber + 1;
                if ($part->subtype == 'PLAIN') {
                    $body = imap_fetchbody($mailbox, $messageNumber, $partNumber);
                    if ($part->encoding == 3) {
                        $body = base64_decode($body);
                    } elseif ($part->encoding == 4) {
                        $body = quoted_printable_decode($body);
                    }
                    return nl2br($body);
                }
            }
        } else {
            // Single part message
            $body = imap_body($mailbox, $messageNumber);
            if ($structure->encoding == 3) {
                $body = base64_decode($body);
            } elseif ($structure->encoding == 4) {
                $body = quoted_printable_decode($body);
            }
            return nl2br($body);
        }

        return null;
    }

    /**
     * Get attachments
     */
    private function getAttachments($mailbox, $messageNumber, $structure)
    {
        $attachments = [];
        
        if (!$structure || !isset($structure->parts)) {
            return $attachments;
        }

        foreach ($structure->parts as $partNumber => $part) {
            $partNumber = $partNumber + 1;
            
            if (isset($part->disposition) && strtolower($part->disposition) == 'attachment') {
                $filename = '';
                if (isset($part->dparameters)) {
                    foreach ($part->dparameters as $param) {
                        if (strtolower($param->attribute) == 'filename') {
                            $filename = $param->value;
                            break;
                        }
                    }
                }
                
                if ($filename) {
                    $attachments[] = [
                        'filename' => $filename,
                        'part_number' => $partNumber,
                    ];
                }
            }
        }

        return $attachments;
    }
}

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
    private $smtpPort = 25;
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
        $folders = ['INBOX', 'Sent'];

        try {
            // Connect to IMAP without TLS/SSL (port 143)
            $mailbox = @imap_open(
                "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}",
                $this->email,
                $this->password
            );

            if ($mailbox) {
                // Select the folder
                $folderPath = "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}{$folder}";
                @imap_reopen($mailbox, $folderPath);
                
                // Get mailbox status
                $status = imap_status($mailbox, $folderPath, SA_ALL);
                $totalEmails = $status->messages ?? 0;

                // Get emails (most recent first)
                $start = max(1, $totalEmails - ($page * $perPage) + 1);
                $end = max(1, $totalEmails - (($page - 1) * $perPage) + 1);

                if ($start <= $end && $totalEmails > 0) {
                    $messageNumbers = range($end, $start);
                    
                    foreach ($messageNumbers as $msgNumber) {
                        $header = imap_headerinfo($mailbox, $msgNumber);
                        if ($header) {
                            // For Sent folder, show "To" instead of "From"
                            if ($folder === 'Sent') {
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
            } else {
                $error = 'Failed to connect to mail server: ' . imap_last_error();
            }
        } catch (\Exception $e) {
            $error = 'Error fetching emails: ' . $e->getMessage();
            Log::error('Mail inbox error: ' . $e->getMessage());
        }

        $totalPages = ceil($totalEmails / $perPage);

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
            Config::set('mail.mailers.smtp.encryption', null);
            Config::set('mail.from.address', $this->email);
            Config::set('mail.from.name', 'Leveler Mail');

            $to = $request->to;
            $subject = $request->subject;
            $body = $request->body;
            $cc = $request->cc;
            $bcc = $request->bcc;

            Mail::send([], [], function ($message) use ($to, $subject, $body, $cc, $bcc) {
                $message->from($this->email, 'Leveler Mail')
                    ->to($to)
                    ->subject($subject)
                    ->html($body);
                
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
                    foreach ($folders as $f) {
                        if (strpos($f, 'Sent') !== false) {
                            $sentFolderExists = true;
                            break;
                        }
                    }
                    
                    if (!$sentFolderExists) {
                        @imap_createmailbox($mailbox, imap_utf7_encode("{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}Sent"));
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
                // Select the correct folder
                $folderPath = "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}{$folder}";
                @imap_reopen($mailbox, $folderPath);
                
                $header = imap_headerinfo($mailbox, $id);
                
                if ($header) {
                    $body = imap_body($mailbox, $id);
                    
                    // Try to get HTML body if available
                    $structure = imap_fetchstructure($mailbox, $id);
                    $htmlBody = $this->getMessageBody($mailbox, $id, $structure);
                    
                    $email = [
                        'number' => $id,
                        'from' => $header->from[0]->mailbox . '@' . $header->from[0]->host,
                        'from_name' => isset($header->from[0]->personal) ? imap_mime_header_decode($header->from[0]->personal)[0]->text : '',
                        'to' => isset($header->to) ? array_map(function($to) {
                            return $to->mailbox . '@' . $to->host;
                        }, $header->to) : [],
                        'cc' => isset($header->cc) ? array_map(function($cc) {
                            return $cc->mailbox . '@' . $cc->host;
                        }, $header->cc) : [],
                        'subject' => isset($header->subject) ? imap_mime_header_decode($header->subject)[0]->text : '(No Subject)',
                        'date' => $header->date,
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
    public function delete($id)
    {
        if (!function_exists('imap_open')) {
            return redirect()->route('admin.mail.index')
                ->with('error', 'PHP IMAP extension is not installed.');
        }

        try {
            $mailbox = @imap_open(
                "{{$this->imapHost}:{$this->imapPort}/imap/notls/novalidate-cert}",
                $this->email,
                $this->password
            );

            if ($mailbox) {
                imap_delete($mailbox, $id);
                imap_expunge($mailbox);
                imap_close($mailbox);
                
                return redirect()->route('admin.mail.index')
                    ->with('success', 'Email deleted successfully!');
            } else {
                return redirect()->route('admin.mail.index')
                    ->with('error', 'Failed to connect to mail server');
            }
        } catch (\Exception $e) {
            Log::error('Mail delete error: ' . $e->getMessage());
            return redirect()->route('admin.mail.index')
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

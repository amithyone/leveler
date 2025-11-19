# PayVibe Payment UI Template

This is a standalone HTML template showcasing the complete PayVibe payment flow from the BiggestLogs application.

## 📋 Overview

The template demonstrates the two-step payment process:

1. **Step 1: Wallet Deposit Form** - User enters amount and selects PayVibe as payment gateway
2. **Step 2: PayVibe Payment Instructions** - Displays virtual account details and payment instructions

## 🎨 Design Features

- **Color Scheme:**
  - Dark background: `#1a1a1a` (dark-100)
  - Card backgrounds: `#2d2d2d` (dark-200), `#3d3d3d` (dark-300)
  - Accent colors: Red (`#ef4444`) and Yellow (`#fbbf24`)
  - Gradient text effects for headings

- **Typography:**
  - Primary font: Proxima Nova
  - Secondary font: Montserrat
  - Font weights: 300-900

- **Interactive Elements:**
  - Glowing button effects with shine animation
  - Copy-to-clipboard functionality for account number and reference
  - Smooth transitions and hover effects
  - Alert notifications (top-right)

## 🔄 Payment Flow

### Step 1: Deposit Form
- User enters amount to fund wallet
- Selects payment gateway (PayVibe)
- Submits form

### Step 2: Payment Instructions
- Displays virtual account details:
  - Bank Name
  - Account Name
  - Account Number (copyable)
- Shows payment summary:
  - Amount to fund
  - Service charges
  - Total to transfer
- Displays reference number (copyable)
- Provides step-by-step instructions
- "I've Made the Transfer" button to check payment status
- Auto-check indicator (simulated)

## 📱 Responsive Design

- Mobile-first approach
- Breakpoints: sm (640px), md (768px), lg (1024px)
- Adaptive padding and font sizes
- Touch-friendly buttons and inputs

## 🎯 Key Components

1. **Wallet Balance Card** - Gradient card showing balance and statistics
2. **Deposit Form** - Input fields for amount and gateway selection
3. **Payment Instructions Card** - Gradient header with account details
4. **Payment Summary** - Breakdown of amount, charges, and total
5. **Reference Section** - Transaction reference with copy button
6. **Instructions Box** - Step-by-step payment guide
7. **Status Check Button** - Manual payment confirmation trigger

## 🚀 Usage

Simply open `payvibe-payment-ui-template.html` in any modern web browser. No server or build process required.

### Testing the Flow:

1. Enter an amount (e.g., 5000)
2. Select "PayVibe" from the gateway dropdown
3. Click "Fund Wallet"
4. View the payment instructions page
5. Test copy buttons for account number and reference
6. Click "I've Made the Transfer" to see status check

## 🎨 Customization

All styles are self-contained in the `<style>` tag. Key customization points:

- Colors: Update CSS variables in `:root`
- Fonts: Modify font imports and font-family declarations
- Spacing: Adjust Tailwind classes or add custom padding/margin
- Animations: Modify keyframe animations in CSS

## 📝 Notes for UI Designer

- The template uses Tailwind CSS via CDN for utility classes
- Custom CSS handles gradients, animations, and special effects
- All interactive elements are functional (copy, form submission, alerts)
- The design follows a dark theme with red/yellow accent colors
- Payment flow is fully interactive for demonstration purposes

## 🔧 Technical Details

- Pure HTML/CSS/JavaScript (no frameworks)
- Tailwind CSS via CDN
- Google Fonts for typography
- Clipboard API for copy functionality
- Responsive grid and flexbox layouts
- CSS animations and transitions

---

**Created for:** BiggestLogs Payment System  
**Template Version:** 1.0  
**Last Updated:** 2024


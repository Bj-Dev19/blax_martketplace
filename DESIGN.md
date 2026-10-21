# Design System: Blax Marketplace (Polomolok Local Commerce & Rush Printing)

## 1. Visual Theme & Atmosphere
Blax Marketplace embodies a **Warm Terra-Bento Utilitarian** atmosphere — blending tactile local community commerce with clinical, high-trust fintech clarity. Surfaces are bright, structured, and calm during daylight (`#F8FAFC` canvas with `#FFFFFF` bento cards) and transition seamlessly to deep slate-forest surfaces (`#0F172A` canvas with `#151F32` cards) in dark mode.
- **Density:** 6/10 (Daily App Balanced — scannable e-commerce cards with crisp metadata dividers)
- **Variance:** 5/10 (Structured Bento Grid with clear visual hierarchy between Step 1 Fulfillment and Step 2 Payment)
- **Motion:** 6/10 (Fluid micro-interactions, tactile `-1px` / `scale-[0.98]` active states, smooth modal scale-up transitions)

## 2. Color Palette & Roles
- **Canvas Alabaster** (`#F8FAFC` / Dark `#0B1120`) — Primary marketplace page background
- **Pure Bento Surface** (`#FFFFFF` / Dark `#0F172A`) — Primary card, modal, and drawer container fill
- **Muted Slate Well** (`#F1F5F9` / Dark `#1E293B`) — Secondary interactive card wells, unselected radio cards, and summary footers
- **Bento Forest Ink** (`#0F172A` / Dark `#F8FAFC`) — Primary typography, product titles, and price figures
- **Muted Steel** (`#64748B` / Dark `#94A3B8`) — Secondary helper copy, subtitles, and fulfillment hints
- **Whisper Structural Border** (`#E2E8F0` / Dark `#1E293B`) — 1px card borders and horizontal item dividers
- **Terra Green (Primary Accent)** (`#4A943E` / Hover `#3B7732` / Light Tint `#EAF4E8`) — Primary CTAs, active radio card borders, selected checkmarks, and verified merchant badges
- **Terra Amber (Fulfillment Accent)** (`#D97706` / Tint `rgba(245, 158, 11, 0.12)`) — Store Pick-up badges, branch indicators, and fulfillment notices
- **PayMongo Cerulean (Payment Accent)** (`#007DFE` / Tint `rgba(0, 125, 254, 0.10)`) — GCash digital wallet indicator and online payment badges
- **Alert Crimson** (`#E11D48`) — Destructive actions, inline validation errors, and stock alerts

## 3. Typography Rules
- **Display & Section Headings:** `Outfit` (`font-display`) — Track-tight (`-0.02em`), bold/black weights (`700`–`900`) for page headers, card titles, and modal headers.
- **Body & UI Controls:** `Plus Jakarta Sans` (`font-sans`) — Clean, high-legibility geometric sans for product descriptions, radio option labels, and helper instructions.
- **Monospace & Financial Data:** `JetBrains Mono` (`font-mono`) — Mandatory for all currency figures (`₱`), unit prices, subtotals, shipping fees, item quantities, and badge counters.
- **Banned:** Generic unstyled serif fonts (`Times New Roman`, `Georgia`).

## 4. Component Stylings
* **Step-by-Step Selection Cards (Fulfillment & Payment):**
  - Generously rounded corners (`1rem` / `rounded-2xl`), `2px` border transition.
  - **Active/Selected State:** Terra Green border (`#4A943E`), subtle Terra Green tint fill (`rgba(74, 148, 62, 0.06)`), visible check indicator, and elevated contrast.
  - **Disabled State:** `40%` opacity, `cursor-not-allowed`, explicit inline explanation of why the option is unavailable for the shop.
* **Buttons:**
  - Primary CTA uses solid Terra Green (`#4A943E`) with crisp white text, subtle elevation shadow, and `active:scale-[0.98]` tactile press feedback. Minimum `44px` height on mobile viewports.
* **Bento Cards:**
  - `1.5rem` (`rounded-3xl`) outer radius, `1px` Whisper Structural Border (`#E2E8F0`), soft diffused shadow.
* **Cart Item Rows:**
  - Fixed-aspect square thumbnail (`w-20 h-20` on mobile, `w-24 h-24` on desktop, `rounded-2xl`, `shrink-0`) paired with a `min-w-0 flex-1` content column that stacks price and title cleanly on narrow screens.
* **Order Review Modal:**
  - Frosted backdrop (`backdrop-blur-sm bg-black/60`), `rounded-3xl` dialog container, structured summary rows showing Receiving Method, Payment Method, Store(s), and Total Payable before final submission. Never auto-submits without explicit user confirmation.

## 5. Layout & Responsive Principles
- **Strict Mobile Containment (`< 768px`):**
  - All 12-column grids collapse to a single vertical column (`grid-cols-1` or `col-span-12`) with `min-w-0` enforced on every grid and flex child to prevent horizontal scrollbars.
  - Zero horizontal overflow (`overflow-x-hidden`) at `320px`, `375px`, and `414px` mobile widths.
- **Touch Target Ergonomics:**
  - Quantity steppers (`−` / `+`), checkboxes, radio cards, and CTA buttons maintain comfortable touch targets (`36px–44px` minimum).
- **Sticky Mobile Bar Alignment:**
  - Mobile sticky checkout drawer aligns flush above the bottom navigation bar (`bottom-14`) on phones (`< 768px`) and drops flush to the bottom edge (`md:bottom-0`) on tablet viewports (`768px–1023px`) where the bottom nav is hidden.

## 6. Anti-Patterns (Banned)
- **NEVER conflate Fulfillment Method and Payment Method** into a single radio group. Receiving Method (*Doorstep Delivery* vs. *Store Pick-up*) must always be Step 1, and Payment Method (*GCash* vs. *Cash*) must always be Step 2.
- **NEVER trigger surprise auto-submit modals** that force the user to pick fulfillment after clicking "Pay" or submit the form the instant an option is clicked without reviewing the updated total.
- **NEVER use non-existent Tailwind v3 sizing classes** such as `w-18` or `h-18` that leave images unconstrained on mobile viewports.
- **NEVER hide critical store fulfillment badges** (`Delivery` / `Pick-up`) on mobile screens.


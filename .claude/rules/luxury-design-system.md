# Luxury Statement Design System
> Applies to: Shopenhood — both frontend (public) and backend (admin panel)

---

## Color Palette

| Token | Hex | Role |
|---|---|---|
| `primary` | `#000000` | Canvas background, deepest layer |
| `secondary` | `#37474F` | Sidebar, nav headers, subtle borders |
| `accent` | `#D4AF37` | Active states, CTAs, KPI highlights, focus rings |
| `background` | `#FFFFFF` | Page background (frontend-backend), modal overlays |
| `neutral` | `#E0E0E0` | Body text on dark, dividers, input borders |
| `text` | `#1A1A1A` | Body text on light backgrounds |

### Forbidden Combinations
- **Never** gold (`#D4AF37`) text on white (`#FFFFFF`) — contrast ratio 1.86:1 (fails WCAG AA)
- **Never** white text on gold background — same issue
- Gold is only used as text/border/icon **on dark surfaces** (`#000000` or `#1A1A1A`)

---

## Typography

| Use | Size | Weight | Color |
|---|---|---|---|
| Page heading (H1) | `clamp(1.25rem, 2.5vw, 2rem)` | 700 | `#FFFFFF` or `#1A1A1A` |
| Section heading (H2) | `clamp(1rem, 2vw, 1.5rem)` | 600 | `#FFFFFF` or `#1A1A1A` |
| Card title (H3) | `clamp(0.875rem, 1.5vw, 1.125rem)` | 600 | `#FFFFFF` or `#1A1A1A` |
| Body text | `0.875rem` (14px) | 400 | `#E0E0E0` (dark bg) / `#1A1A1A` (light bg) |
| Label / Caption | `0.75rem` (12px) | 400 | `#E0E0E0` (dark bg) / `#37474F` (light bg) |
| Table cell | `0.8125rem` (13px) | 400 | `#E0E0E0` |
| Button text | `0.8125rem` (13px) | 600 | matches button style |

**Font family:** System font stack — `'Inter', 'Segoe UI', sans-serif`

---

## Spacing — 4px Modular Grid

All spacing must be a multiple of 4px.

| Token | Value | Use |
|---|---|---|
| `space-1` | `4px` | Icon-to-label gap, tight inline padding |
| `space-2` | `8px` | Compact table row padding, checkbox lists |
| `space-3` | `12px` | Card padding, horizontal gutters |
| `space-4` | `16px` | Section spacing, input padding |
| `space-5` | `20px` | Card gap in grids |
| `space-6` | `24px` | Dashboard top margin (desktop only) |

---

## Component Dimensions (Compact-First)

### Buttons

| Size | Height | Padding (H) | Font | Use |
|---|---|---|---|---|
| `sm` | `28px` | `10px` | `12px` | Table row actions, tight spaces |
| `md` | `34px` | `14px` | `13px` | Default — forms, cards |
| `lg` | `40px` | `18px` | `14px` | Primary CTA, hero sections |

**Styles:**
- **Primary:** `bg:#D4AF37` · `text:#000000` · `border:none` · `hover: brightness(1.1)`
- **Secondary:** `bg:transparent` · `text:#D4AF37` · `border:1px solid #D4AF37` · `hover: bg:#D4AF37/10`
- **Danger:** `bg:#C0392B` · `text:#FFFFFF`
- **Ghost:** `bg:transparent` · `text:#E0E0E0` · no border · `hover: bg:#FFFFFF/5`
- Border radius: `4px` (compact, not pill-shaped)

### Inputs & Selects

| Property | Value |
|---|---|
| Height | `34px` |
| Padding | `8px 12px` |
| Font size | `13px` |
| Background | `#1A1A1A` (admin) / `#FFFFFF` (frontend) |
| Border | `1px solid #37474F` |
| Border (focus) | `1px solid #D4AF37` |
| Border radius | `4px` |
| Text color | `#E0E0E0` (admin) / `#1A1A1A` (frontend) |
| Placeholder | `#37474F` |

### Cards

| Property | Admin (dark) | Frontend (light) |
|---|---|---|
| Background | `#1A1A1A` | `#FFFFFF` |
| Border | `1px solid #37474F` | `1px solid #E0E0E0` |
| Border radius | `6px` | `8px` |
| Padding | `16px` | `20px` |
| Shadow | `none` | `0 1px 4px rgba(0,0,0,0.08)` |

### Tables (Admin)

| Property | Value |
|---|---|
| Row height | `40px` |
| Header bg | `#37474F` |
| Header text | `#D4AF37` · `12px` · `600` · uppercase |
| Row bg (even) | `#1A1A1A` |
| Row bg (odd) | `#0D0D0D` |
| Row hover | `#37474F/40` |
| Cell padding | `8px 12px` |
| Cell text | `#E0E0E0` · `13px` |
| Border | `1px solid #37474F` on header bottom only |

### Badges / Status Pills

| Property | Value |
|---|---|
| Height | `20px` |
| Padding | `2px 8px` |
| Font size | `11px` · `600` |
| Border radius | `10px` |

Status colors: `active → #D4AF37/20 + #D4AF37 text` · `pending → #37474F + #E0E0E0 text` · `rejected → #C0392B/20 + #E74C3C text`

---

## Layout — Admin Sidebar

| Property | Value |
|---|---|
| Width (expanded) | `220px` |
| Width (collapsed) | `56px` |
| Background | `#37474F` |
| Top logo bar bg | `#000000` |
| Logo bar height | `52px` |
| Nav item height | `36px` |
| Nav item padding | `8px 12px` |
| Nav item text | `13px` · `#E0E0E0` |
| Nav item (active) | left border `3px solid #D4AF37` · `bg:#000000/30` · text `#D4AF37` |
| Nav item (hover) | `bg:#000000/20` |
| Section label | `10px` · `#37474F` · uppercase · `600` |

---

## Responsive Breakpoints

| Tier | Width | Layout |
|---|---|---|
| **Mobile** | `< 480px` | 1 column · hamburger nav · full-width cards · `space-3` gutters |
| **Tablet (P)** | `481–768px` | 2 columns · mini sidebar (icons only, 56px) · `space-3` gutters |
| **Tablet (L) / Laptop** | `769–1024px` | 3 columns · sidebar 180px · `space-4` gutters |
| **Desktop** | `1025–1535px` | 4 columns · sidebar 220px · `space-5` gutters |
| **Large / TV** | `> 1536px` | 12-col grid · wide sidebar · `space-6` gutters · font scale +20% |

### TV / 10-Foot UI Rules
- Minimum body font: `24px` · Minimum heading: `36px`
- All sides: `90px` overscan margin
- Focus ring: `3px solid #D4AF37` with `box-shadow: 0 0 0 4px rgba(212,175,55,0.3)`
- No CSS animations — only instant state changes
- No element under `52px` tall (D-pad navigation target size)

---

## Admin Panel — Surface Hierarchy

```
#000000  ← Page canvas (outermost bg)
  └─ #1A1A1A  ← Cards, tables, modals
       └─ #37474F  ← Sidebar, sub-panels, input borders
            └─ #D4AF37  ← Active indicator, gold accent
```

---

## Frontend — Surface Hierarchy

```
#FFFFFF  ← Page background
  └─ #FFFFFF  ← Cards (with E0E0E0 border)
       └─ #E0E0E0  ← Dividers, input borders
            └─ #D4AF37  ← CTAs, price highlights, active nav
```

---

## Dashboard Information Hierarchy (Inverted Pyramid)

1. **KPI Row (top):** 4–6 metric tiles · gold value text (`#D4AF37`) · large `clamp(1.25rem, 2vw, 1.75rem)` · compact `40px` tile height
2. **Charts / Trends (middle):** 2–3 widgets · `#E0E0E0` axes labels · subtle grid lines `#37474F/30`
3. **Data Tables (bottom):** Dense `40px` rows · `8px` row gaps · alternating `#1A1A1A` / `#0D0D0D`

---

## Do / Don't

| Do | Don't |
|---|---|
| Use `#D4AF37` as border, icon, or text on dark surfaces | Use gold as button background with white text |
| Keep buttons `28–40px` tall | Make buttons taller than `44px` in dense admin tables |
| Use `4px` border-radius on admin elements | Use pill shapes (high border-radius) in admin |
| Use `box-shadow` only on frontend cards | Add shadows inside the dark admin canvas |
| Use `gap` utilities for spacing between items | Use `margin` on individual items in a grid/flex row |
| Scale typography with `clamp()` | Define fixed `px` sizes for headings without responsive fallback |

---

## Tailwind CSS Custom Config (Excerpt)

```js
// tailwind.config.js
theme: {
  extend: {
    colors: {
      luxury: {
        black:   '#000000',
        surface: '#1A1A1A',
        charcoal:'#37474F',
        gold:    '#D4AF37',
        light:   '#E0E0E0',
        white:   '#FFFFFF',
        text:    '#1A1A1A',
      }
    },
    spacing: {
      '1': '4px',
      '2': '8px',
      '3': '12px',
      '4': '16px',
      '5': '20px',
      '6': '24px',
    },
    borderRadius: {
      DEFAULT: '4px',
      card: '6px',
      pill: '9999px',
    },
    fontSize: {
      'label': ['0.75rem', { lineHeight: '1rem' }],
      'body':  ['0.875rem', { lineHeight: '1.25rem' }],
      'ui':    ['0.8125rem', { lineHeight: '1.25rem' }],
    }
  }
}
```

---

## Button Consistency Rules

All buttons across the application must follow a unified design system. Inconsistent implementations (e.g., using plain `<a>` tags without defined width/height) are not allowed, as they create visual clutter and layout issues.

### Requirements

* All buttons must use a consistent component structure (shared Blade component, reusable class, or UI component).
* `<a>`, `<button>`, or any clickable element styled as a button must follow the same base styles.
* Every button must have:

  * Defined padding (e.g., `px-4 py-2`)
  * Consistent height
  * Proper alignment (`inline-flex items-center justify-center`)
  * Spacing between icon and text (`gap-2`)
  * Border radius (e.g., `rounded-xl`)
* Buttons must NOT break layout or stack incorrectly due to missing styles.
* Avoid raw, unstyled `<a>` elements acting as buttons.

### Standard Example

```html
<a href="#" class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-primary text-white gap-2">
    <i class="fa-solid fa-plus"></i>
    Add Item
</a>
```

---

## Icon Standardization Rules

Emoji usage (e.g., 🌳, 📁, 🔧) is not allowed in the UI. All such icons must be replaced with a consistent icon system.

### Requirements

* Replace ALL emojis with icons from a unified library (recommended: Font Awesome).
* Perform a full scan of the project and remove all emoji-based UI elements.
* Use meaningful icons that match the context (action, content, status).
* Ensure icons are used consistently across all pages.

### Font Awesome Integration

Add the CDN to the main layout:

```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
```

### Example Replacements

* 🌳 → `<i class="fa-solid fa-tree"></i>`
* 📁 → `<i class="fa-solid fa-folder"></i>`
* 🔧 → `<i class="fa-solid fa-screwdriver-wrench"></i>`

### Icon Usage Standard

```html
<i class="fa-solid fa-folder text-lg"></i>
```



*Use this file as the single source of truth when prompting Claude.ai to generate or refine any UI component — always reference the token names above instead of raw hex values.*

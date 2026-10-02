# Homepage — `/`

> Draft to be refined together. Describes the desired UI and behaviour of the public landing page.

## Purpose
Public landing page: a hero slider of featured listings with an overlaid search, an agency intro, a multilingual
section and a final call to action. Rendered in the public shell (`x-public-layout`, via its full-width `hero` slot).

## Access rules
- Public (no authentication).

## Content
- **Hero slider** (full-bleed): up to 6 listings (active + not sold, `is_featured` first), each a full-bleed image
  with name + price, linking to its detail page; auto-advances (~5s, pauses on hover) with ‹/› arrows and dots.
- **Search**: the compact `x-property-search` (keyword + type + area), overlaid bottom-centre of the slider on
  desktop; stacked just below on mobile.
- **Agency intro**: short blurb linking to `/about`.
- **"We are multilingual & multicultural"**: 5 flags (USA, Mexico, France, Germany, Italy) + names.
- **Final CTA**: Browse all (`/properties`) / Contact (`/contact`).

## Notes
- Flag SVGs are **vendored** in `public/images/flags/` (from `flag-icons`, MIT) — only the 5 needed, to avoid bundling
  the whole library (which is ~5.7 MB / 542 files).
- Only approved listings appear in the slider.

## Wanted changes
> Describe the desired content / UI changes here.
-

## Out of scope
- Testimonials, newsletter, localized copy (LOC-01).

# Magyar Counter-Strike e-sport — Webfejlesztés 1 Beadandó

Többoldalas, szabványos, akadálymentesített és reszponzív weboldal a magyar Counter-Strike e-sport történetéről, nemzetközi szinten elismert játékosairól (torzsi, sl3nd, DeadFox) és a MOUZ csapatáról.

Készült a Webfejlesztés 1 kurzus egyéni feladataként az ELTE Informatikai Karán, a megadott 47 szempontos szakmai és akadálymentesítési irányelvrendszer szerint.

## Főbb technikai megvalósítások

- **Szemantikus HTML5 felépítés:** `<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<aside>`, `<figure>`, `<footer>`, `<video>`.
- **Reszponzív rácsrendszer:** Bootstrap 5 keretrendszer flexibilis oszlopkiosztással (mobil, tablet, asztali nézet).
- **Elkülönített stíluslapok:** 
  - `style.css`: 25+ egyedi CSS szabály a Bootstrap stílusok felülírására és egyedi sötét témára.
  - `print.css`: dedikált nyomtatási stíluslap (navigáció és videók elrejtése, gazdaságos fekete-fehér megjelenés, URL-ek kiírása).
- **Akadálymentesség és UX (WCAG 2.0):**
  - Képernyőolvasóknak szánt ugróhivatkozás a fő tartalomra (`skip-link`).
  - Dinamikus betűméret-állítás (A- / A / A+) `localStorage` perzisztenciával.
  - Nagy kontrasztú mód kapcsoló (`contrastToggle`).
  - Lenyitható táblázatfeliratok (`<caption>` `<details>`-szel kombinálva).
  - Szabványos, akadálymentes visszajelző űrlap (`form.html`) asszociált címkékkel (`label for`), határolt beviteli mezőkkel és `mailto:` beküldéssel.

## Oldalak szerkezete

1. `index.html` — A hazai e-sport története, DeadFox Major szereplése, PERA csapat és jövőkép.
2. `torzsi.html` — Torzsás Ádám karrierje, csapatai, képgaléria, videó-klip és eredménytáblázatok.
3. `slend.html` — Hevesi Henrich bemutatása, csapatai és eredményei.
4. `mouz.html` — A MOUZ és MOUZ NXT története, trófealista és hivatalos logó.
5. `form.html` — Közösségi véleményező űrlap.
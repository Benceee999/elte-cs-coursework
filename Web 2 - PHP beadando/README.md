# PartyZone Adminisztrációs Rendszer (PHP & JSON Storage)

A PartyZone bulvárlap szerveroldali adminisztrációs felülete celebek, platformok és kategóriák rendszerezésére. Készült a Webfejlesztés 2 kurzus szerveroldali beadandó feladataként, tisztán natív PHP, JSON alapú perzisztens adattárolás és CSS3 felhasználásával, JavaScript nélkül.

## Megvalósított funkciók

### Alapkövetelmények & Jogosultságok
- **Szerepkör-alapú hozzáférés-vezérlés (RBAC):**
  - **Guest (Vendég):** Lista megtekintése, celebek keresése és szűrése, részletező oldal megtekintése.
  - **User (Bejelentkezett):** Új celebek hozzáadása és meglévők szerkesztése.
  - **Admin:** Rendszerkezelő panel (platformtípusok és platformok létrehozása, szerkesztése).
- **Hitelesítés (Auth):**
  - Regisztráció formátum- és egyediség-ellenőrzéssel, jelszavak biztonságos hashelésével (`password_hash`, `PASSWORD_DEFAULT`).
  - Bejelentkezés session-alapú azonosítással, biztonságos kijelentkezés (`session_destroy`).
- **Összetett GET paraméteres szűrés:**
  - Név-részletkeresés több-bájtos UTF-8 támogatással (`mb_strtolower`, `str_contains`).
  - Szűrés platformra vagy platformtípusra, kombinálva a szöveges kereséssel (logikai ÉS kapcsolat).
- **Perzisztens adattárolás:**
  - `Storage` és `JsonIO` rétegen keresztül kezelt JSON fájlok (`data_*.json`).
  - Automatikus kezdőadat-feltöltés (`seed`), amennyiben az adatbázisok még üresek.

### Megvalósított bónusz funkciók (+11 pont)
1. **Admin felhasználó (+2 pont):** Automatikusan inicializált beépített admin fiók (`admin` / `admin`).
2. **Celebtípusok kezelése (+2 pont):** Külön kezelt celebkategóriák hozzáadása és szerkesztése admin jogosultsággal, kategória választása celeb rögzítésekor, és megjelenítése a listában.
3. **Platformok és platformtípusok törlése relációs integritással (+2 pont):**
   - Platform csak akkor törölhető, ha nincs hozzá rendelve egyetlen celeb sem.
   - Platformtípus csak akkor törölhető, ha nem tartozik hozzá regisztrált platform.
4. **Celebek törlése jogosultság szerint (+2 pont):**
   - Az adminisztrátor bármelyik celebet törölheti.
   - A bejelentkezett felhasználók kizárólag azokat a celebeket törölhetik, amelyeket ők maguk hoztak létre (`created_by` azonosító alapján).
5. **Keresés kattintással (+3 pont):** A celeb melletti színes platformcímkékre kattintva az oldal automatikusan betölti a szűrőt az adott platformra.

## Futtatás

Helyi PHP fejlesztői szerver indítása a projekt mappájából:

```bash
php -S localhost:8000
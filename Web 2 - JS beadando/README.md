# PartyZone Adminisztrációs Rendszer

A PartyZone bulvárlap adminisztrációs felülete celebek, platformok és platformtípusok rendszerezésére, szűrésére és statisztikai elemzésére. Készült a Webfejlesztés 2 kurzus kliensoldali feladataként, tisztán natív JavaScript (Vanilla JS), HTML5 és CSS3 technológiákkal, külső függőségek nélkül.

## Megvalósított funkciók

### Alapkövetelmények
- **Lapfülek kezelése (SPA működés):** Három különálló nézet (Celebek, Platformok & Típusok, Statisztikák) JavaScript-alapú fülváltással.
- **Entitások kezelése (CRUD):**
  - Platformtípusok létrehozása és szerkesztése (egyedi név validációval).
  - Platformok létrehozása és szerkesztése (egyedi név, színválasztó és létező típushoz kötés).
  - Celebek létrehozása és szerkesztése (név, típus és legalább egy platform kijelölése kötelező).
- **Összetett keresés és szűrés:**
  - Név-részletkeresés valós időben.
  - Szűrés konkrét platformra VAGY platformtípusra.
  - A névre és platformra történő szűrések logikai **ÉS** kapcsolattal kombinálódnak.
- **Statisztikai modul:**
  - Három alapvető aggregált táblázat: celebek száma platformonként, celebek száma típusonként, platformok száma típusonként.
  - Vizuális arány-megjelenítés karakteres diagramokkal (`█`).

### Megvalósított bónusz funkciók (+10 pont)
1. **Statisztika rendezés (+1 pont):** Mindegyik statisztikai táblázat oszlopfejlécre kattintva növekvő és csökkenő sorrendbe rendezhető, harmadik kattintásra visszaáll az eredeti sorrend.
2. **Celebtípusok kezelése (+2 pont):** Külön entitásként felvehetők és szerkeszthetők a celebtípusok, hozzárendelhetők a celebekhez, és egy 4. dedikált táblázatban jelennek meg a statisztikák között.
3. **Függőségvizsgáló törlés (+2 pont):**
   - Celebek közvetlenül törölhetők megerősítés után.
   - Platform csak akkor törölhető, ha egyetlen celeb sem használja.
   - Platformtípus csak akkor törölhető, ha nem tartozik alá regisztrált platform.
4. **LocalStorage használata (+2 pont):** Minden módosítás (létrehozás, szerkesztés, törlés) automatikusan szinkronizálódik a böngésző helyi tárhelyébe, így oldalfrissítés után is megmarad az állapot.
5. **Keresés kattintással (+3 pont):** A celeb melletti színes platformcímkékre kattintva a felület automatikusan beállítja a platformszűrőt és lefuttatja a listázást.

## Futtatás

Mivel a projekt külső build-eszközt vagy keretrendszert nem igényel, egyszerűen nyisd meg az `index.html` fájlt bármely modern webböngészőben.
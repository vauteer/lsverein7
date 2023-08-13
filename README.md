## Über LS-Verein 7

'LS-Verein 7' ist eine übersichtliche Webanwendung 
mit der man die Mitglieder eines Vereins verwalten kann.
Es ist eine 'Single-Page-Application' und kann per Browser von verschiedenen Endgeräten benutzt werden.
### Enthaltene Funktionen:
- Eingabe der Stammdaten
- Benutzerverwaltung mit Rechte-Management (Rollen)
- Zeitliche Zuordnung zu Abteilungen (Sparten)
- Zeitliches Festhalten von Funktionen (Ämtern)
- Zeitliches Festhalten von Ereignisen (Ehrungen)
- Zeitliches Festhalten von Inventar
- Zuordnen von Mitgliedsbeiträgen
- Abbuchen der Mitgliedsbeiträge (Sepa-Datei)
- Erstellen von einmaligen Lastschriften (Sepa-Datei)
- Erstellung einer Statistik für die jährliche BLSV-Meldung
- Exportieren der Mitglieder als PDF, CSV und vCard
- Die Verwaltung mehrerer Vereine ist möglich
## Voraussetzungen
1. PHP 8.1+
2. Composer
3. Node (mit npm)
4. MySQL 5.7+ oder MariaDB 10.10+

## Installation
1. Das Projekt clonen/installieren
2. In das Projekt-Verzeichnis wechseln
3. Die Konfigurations-Datei erzeugen<br>cp .env.example .env
1. composer install --optimize-autoloader --no-dev
4. php artisan key:generate
5. Eine leere MySQL Datenbank erzeugen
7. Die Konfigurations-Datei anpassen (Datenbank, Titel, ...)
8. php artisan migrate
9. Einen Administrator anlegen<br>php artisan app:user 'Max Mustermann' 'max@mustermann.de' --password=******** --admin
9. npm install
10. npm run build
11. Eine Domain/Subdomain einrichten<br>Dokumentenstamm ist das 'public' Verzeichnis! 
12. Mit den Administrator-Daten anmelden

## Wiederherstellen eines Backups
Machen Sie ein Backup, falls sie Daten überschreiben!
1. Installieren der Anwendung, falls nötig
2. Das löschen der aktuellen Daten ist nicht nötig, weil die komplette Datenbank überschrieben wird!
2. Mit phpMyAdmin oder ähnlichen Programmen das Export-Script importieren.

## Wiederherstellen eines Exports
Machen Sie ein Backup, falls sie Daten überschreiben!<br>Die Export-Datei enthält nur die Daten eines Vereins!
1. Installieren der Anwendung, falls nötig
2. Löschen der aktuellen Daten<br>php artisan migrate:fresh
3. Mit phpMyAdmin oder ähnlichen Programmen das Export-Script importieren.

## Benutzte Frameworks und Tools
- [Laravel](https://laravel.com)
- [Vue.js](https://vuejs.org)
- [Inertia.js](https://inertiajs.com)
- [tailwindcss](https://tailwindcss.com)
- [Vite](https://vitejs.dev)
- und viele mehr

This web application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# WebSupportDesk PopUpSeatch (Magento 2)

Basis-module voor een toekomstige popup-search functionaliteit.

## Module info

- **Module name:** `WebSupportDesk_PopUpSeatch`
- **Package:** `websupportdesk/module-popup-seatch`
- **Versie:** `1.0.0`

## Installatie

```bash
bin/magento module:enable WebSupportDesk_PopUpSeatch
bin/magento setup:upgrade
bin/magento cache:flush
```

## Volgende stap (uitbreiding)

Je kunt nu hierop bouwen met bijvoorbeeld:

- `view/frontend/layout/default.xml` of `catalogsearch_result_index.xml`
- een eigen Knockout/JS component voor popup zoekvenster
- controller of AJAX endpoint voor suggesties
- admin config in `etc/adminhtml/system.xml` (aan/uit, max suggesties, etc.)


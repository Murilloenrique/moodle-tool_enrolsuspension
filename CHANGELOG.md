# Changelog

## 1.1.7-beta - 2026-08-18

- Corrige atualização de instalações existentes ao adicionar capabilities granulares.
- Remove atribuição prematura de capabilities dentro de `db/upgrade.php`.
- Usa `clonepermissionsfrom` em `db/access.php` para preservar permissões da capability legada `manage` durante o upgrade.
- Mantém a correção PSR-12 da tela de revisão validada pelo Moodle Plugin CI.

## 1.1.4-beta — 2026-08-17

- Corrige o padrão de cabeçalho/licença exigido pelo Moodle Code Checker.
- Corrige a formatação PSR-2/Moodle de chamadas multilinha em `courses.php`.

## 1.1.1-beta - 2026-08-17

- Renamed all plugin-owned database tables to use the full `tool_enrolsuspension` Frankenstyle prefix.
- Added an upgrade migration that preserves existing audit and workflow data while renaming tables.
- Recreated workflow foreign keys after table migration.
- Fixed strict PHPUnit user ID type assertions.
- Addresses the Moodle Plugin CI `validate` table-prefix failure.

## 1.1.0-beta - 2026-08-17

- Isola cada fluxo de suspensão em uma operação identificada por token aleatório.
- Torna a confirmação de suspensão de uso único e revalida os vínculos exatos revisados.
- Impede mais de uma suspensão ativa do plugin para o mesmo `user_enrolments.id`.
- Reativa somente o vínculo histórico original e marca registros obsoletos quando ele não existe mais.
- Restringe alterações aos métodos de inscrição `manual` e `self`, respeitando `allow_manage()` e capabilities do método.
- Completa os principais contratos da Privacy API, incluindo `core_userlist_provider`.
- Unifica a definição de inscrição ativa entre seleção e execução.
- Adiciona paginação e exportação CSV ao histórico.
- Reescreve a importação com `csv_import_reader`, consultas em lote e detecção de ambiguidades.
- Separa capabilities de visualização, suspensão, reativação e importação e declara riscos apropriados.
- Exige POST e `sesskey` nos endpoints que alteram estado.
- Melhora mensagens de falha parcial na reativação.
- Adiciona testes PHPUnit iniciais e workflow de Moodle Plugin CI para Moodle 4.4 e 4.5.
- Adiciona tarefa agendada para limpar operações temporárias expiradas.

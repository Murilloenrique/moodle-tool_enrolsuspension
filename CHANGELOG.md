# Changelog

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

# Controle de Suspensões de Inscrições

Plugin administrativo para Moodle desenvolvido para facilitar a suspensão e reativação de inscrições de usuários em múltiplas disciplinas.

## O problema

Em ambientes Moodle com muitos alunos e disciplinas, suspender o acesso de um usuário pode exigir diversas operações manuais.

O administrador precisa localizar o aluno, identificar as disciplinas em que está matriculado e gerenciar cada vínculo de inscrição individualmente. Quando vários alunos precisam ser suspensos, esse processo se torna ainda mais trabalhoso e sujeito a erros.

## A solução

O **Controle de Suspensões de Inscrições** centraliza essas operações em uma única ferramenta administrativa.

O plugin permite pesquisar e selecionar vários alunos, identificar automaticamente suas disciplinas e escolher quais inscrições devem ser suspensas.

Quando vários alunos são selecionados, o sistema também informa quais deles estão matriculados em cada disciplina.

A suspensão não exclui a conta do usuário nem remove definitivamente sua matrícula. Os vínculos permanecem registrados no Moodle em estado suspenso e podem posteriormente ser reativados.

## Principais recursos

- Pesquisa dinâmica de usuários via AJAX.
- Seleção de múltiplos alunos.
- Identificação automática das disciplinas de cada aluno.
- Identificação dos alunos matriculados em cada disciplina.
- Seleção individual das disciplinas que serão afetadas.
- Suspensão em todas as disciplinas atuais do aluno.
- Suspensão permanente dos vínculos de inscrição.
- Registro obrigatório do motivo da suspensão.
- Revisão antes da confirmação.
- Histórico das suspensões realizadas.
- Reativação de inscrições suspensas.
- Importação de usuários por CSV/TXT.
- Atalho para a ferramenta dentro das disciplinas.

## Compatibilidade

Desenvolvido para:

- Moodle 4.4.x
- Moodle 4.5.x

Componente:

```text
tool_enrolsuspension
```

Tipo:

```text
Admin tool
```

## Instalação

1. Baixe o arquivo ZIP do plugin.
2. Acesse o Moodle como administrador.
3. Vá até **Administração do site > Plugins > Instalar plugins**.
4. Envie o arquivo ZIP.
5. Conclua a instalação ou atualização solicitada pelo Moodle.
6. Limpe os caches, caso necessário.

## Permissões

O acesso às funcionalidades administrativas é controlado pela capability:

```text
tool/enrolsuspension:manage
```

Somente usuários autorizados devem possuir permissão para realizar suspensões e reativações.

## Aviso

Recomenda-se testar o plugin em um ambiente de homologação antes de utilizá-lo em produção.

Métodos de inscrição ou integrações personalizadas podem possuir comportamentos específicos que devem ser validados em cada ambiente Moodle.

## Licença

Este projeto é distribuído sob a licença **GNU GPL v3 ou posterior**.
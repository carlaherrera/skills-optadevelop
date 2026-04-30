# skills-optadevelop

Repositório de skills para agentes de IA — Claude Code, Codex, Gemini, OpenCode, Cursor, Antigravity e outros.

## Skills Disponíveis

| Skill | Descrição |
|-------|-----------|
| [elementor-dev](skills/elementor-dev/) | Desenvolvimento para o ecossistema Elementor completo — widgets, addons, controles, dynamic tags, forms, theme builder |

## Instalação

### Instalar uma skill específica

```bash
npx skills add optadevelop/skills-optadevelop --skill elementor-dev
```

### Instalar no projeto (padrão)

```bash
npx skills add optadevelop/skills-optadevelop --skill elementor-dev
```

### Instalar globalmente

```bash
npx skills add optadevelop/skills-optadevelop --skill elementor-dev -g
```

### Instalar para agente específico

```bash
npx skills add optadevelop/skills-optadevelop --skill elementor-dev -a claude-code
npx skills add optadevelop/skills-optadevelop --skill elementor-dev -a codex
npx skills add optadevelop/skills-optadevelop --skill elementor-dev -a cursor
npx skills add optadevelop/skills-optadevelop --skill elementor-dev -a opencode
```

### Instalar todas as skills

```bash
npx skills add optadevelop/skills-optadevelop --skill '*'
```

### Listar skills disponíveis

```bash
npx skills add optadevelop/skills-optadevelop --list
```

## Estrutura

```
skills-optadevelop/
├── skills/
│   ├── elementor-dev/
│   │   ├── SKILL.md           # Instrução principal
│   │   ├── README.md          # Docs da skill
│   │   ├── references/        # Documentação detalhada
│   │   ├── templates/         # Templates de código
│   │   ├── evals/             # Testes de validação
│   │   ├── agents/            # Configs por agente
│   │   ├── scripts/           # Scripts utilitários
│   │   └── assets/            # Assets auxiliares
│   └── (futuras skills...)
├── README.md
├── LICENSE
└── .gitignore
```

## Como Criar uma Nova Skill

1. Crie uma pasta em `skills/nome-da-skill/`
2. Adicione um `SKILL.md` com frontmatter YAML:

```yaml
---
name: nome-da-skill
description: O que essa skill faz e quando usar
metadata:
  author: optadevelop
  version: 1.0.0
license: MIT
---

# Nome da Skill

Instruções para a IA...
```

3. Teste localmente: `npx skills add ./skills/nome-da-skill --list`
4. Commit e push

## Fontes

- [Elementor Developer Docs](https://github.com/elementor/elementor-developers-docs)

# elementor-dev

Skill de desenvolvimento para o ecossistema Elementor — compatível com Claude, GPT, Gemini, Codex, OpenCode, Antigravity e outras IAs.

## Instalação

### Via Skills CLI (recomendado)

```bash
# Instalar no projeto atual
npx skills add seu-usuario/elementor-dev --skill elementor-dev

# Instalar globalmente (disponível em todos os projetos)
npx skills add seu-usuario/elementor-dev --skill elementor-dev -g

# Instalar para agente específico
npx skills add seu-usuario/elementor-dev --skill elementor-dev -a claude-code
npx skills add seu-usuario/elementor-dev --skill elementor-dev -a codex
npx skills add seu-usuario/elementor-dev --skill elementor-dev -a cursor
```

### Manual

Copie a pasta `elementor-dev/` para o diretório de skills do seu agente:

| Agente | Projeto | Global |
|--------|---------|--------|
| Claude Code | `.claude/skills/elementor-dev/` | `~/.claude/skills/elementor-dev/` |
| OpenCode | `.agents/skills/elementor-dev/` | `~/.config/opencode/skills/elementor-dev/` |
| Cursor | `.cursor/skills/elementor-dev/` | `~/.cursor/skills/elementor-dev/` |

## O que está incluído

- **SKILL.md** — Instrução principal para a IA (~15KB de referência)
- **references/** — 6 documentos detalhados (widgets, controles, hooks, addons, componentes, scripts)
- **templates/** — 9 templates de código prontos (widget, addon, controle, dynamic tag, form action, etc.)
- **evals/** — 8 testes de validação + checklist geral
- **scripts/** — Script CLI de validação de addons
- **agents/** — Configs otimizadas para 6 agentes diferentes
- **assets/** — CSS de debug para editor

## Cobertura do ecossistema

Widgets, Controles (35+ tipos), Group Controls, Dynamic Tags, Form Actions, Form Fields, Theme Conditions, Theme Locations, Finder, Context Menu, Hooks (80+ PHP/JS), Scripts & Styles, Deprecations, Migrações.

## Fonte

Documentação baseada no [elementor-developers-docs](https://github.com/elementor/elementor-developers-docs) oficial.

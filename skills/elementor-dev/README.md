# elementor-dev

Skill de desenvolvimento para o ecossistema Elementor — compatível com Claude, GPT, Gemini, Codex, OpenCode, Antigravity e outras IAs.

## Instalação

### Via Skills CLI (recomendado)

```bash
npx skills add carlaherrera/skills-optadevelop --skill elementor-dev

npx skills add carlaherrera/skills-optadevelop --skill elementor-dev -g

npx skills add carlaherrera/skills-optadevelop --skill elementor-dev -a claude-code
npx skills add carlaherrera/skills-optadevelop --skill elementor-dev -a codex
npx skills add carlaherrera/skills-optadevelop --skill elementor-dev -a cursor
```

### Manual

Copie a pasta `elementor-dev/` para o diretório de skills do seu agente:

| Agente | Projeto | Global |
|--------|---------|--------|
| Claude Code | `.claude/skills/elementor-dev/` | `~/.claude/skills/elementor-dev/` |
| OpenCode | `.agents/skills/elementor-dev/` | `~/.config/opencode/skills/elementor-dev/` |
| Cursor | `.cursor/skills/elementor-dev/` | `~/.cursor/skills/elementor-dev/` |

## O que está incluído

- **SKILL.md** — Instrução principal para a IA (~25KB de referência)
- **references/** — 12 documentos detalhados
- **templates/** — 13 templates de código prontos
- **evals/** — 12 testes de validação
- **scripts/** — Script CLI de validação de addons
- **agents/** — Configs otimizadas para 6 agentes diferentes
- **assets/** — CSS de debug para editor

## Cobertura do ecossistema

### Widgets e Controles
Widgets (simples/avançados), Controles (35+ tipos), Group Controls, Responsive Controls, Inline Editing, Conditional Display, Global Styles, Output Caching.

### CPTs e Itens Personalizados
Custom Post Types (registro, suporte Elementor, taxonomias, permissões, status customizados), Widgets de Query (Group_Control_Query, WP_Query, tax/meta queries, paginação, caching), Skins (Grid, List, Masonry), Tabelas Customizadas (dbDelta, CRUD, meta boxes, admin pages, custom columns).

### Integração com Theme Builder
Theme Conditions (singular, archive, taxonomia, meta field), Theme Locations, Widgets de Info do CPT, Breadcrumbs.

### Dynamic Tags
Post Meta, Taxonomias, Autor, Dados de Tabelas Customizadas (agregações), Thumbnail com Fallback, Integração ACF.

### Controles Customizados para CPTs
Post Type Select, Taxonomy Select, Post Select (AJAX search), Term Select Hierárquico, SELECT2 nativo.

### Forms (Elementor Pro)
Form Actions, Form Fields, Validação, Injeção de Controles.

### Outros
Theme Conditions, Theme Locations, Finder, Context Menu, Hooks (90+ PHP/JS), Scripts & Styles, Deprecations, Migrações.

## Referências

| Arquivo | Conteúdo |
|---------|----------|
| `references/widget-architecture.md` | Ciclo de vida, métodos, renderização, caching |
| `references/control-types.md` | 35+ tipos de controles com exemplos |
| `references/hooks-reference.md` | 90+ hooks PHP/JS organizados por categoria |
| `references/addon-architecture.md` | Singleton, compatibilidade, namespaces |
| `references/components-reference.md` | Dynamic tags, forms, conditions, finder |
| `references/scripts-styles.md` | Sistema de assets, frontend handlers |
| `references/cpt-taxonomy.md` | CPTs, taxonomias, CPT Manager, permissões |
| `references/query-widgets.md` | Widgets de query, skins, Group_Control_Query |
| `references/custom-tables.md` | Tabelas customizadas, CRUD, meta boxes |
| `references/cpt-theme-builder.md` | Theme Builder conditions/locations para CPTs |
| `references/cpt-dynamic-tags.md` | Dynamic tags para CPT, ACF, tabelas |
| `references/cpt-controls.md` | Controles customizados para CPT |

## Fonte

Documentação baseada no [elementor-developers-docs](https://github.com/elementor/elementor-developers-docs) oficial.

# Elementor Developer Skill - CHANGELOG

## 1.0.0 (2026-04-30)

### Conteúdo
- SKILL.md principal com referência completa da API Elementor
- 6 referências detalhadas:
  - widget-architecture.md: ciclo de vida, métodos, renderização, caching
  - control-types.md: 35+ tipos de controles com exemplos
  - hooks-reference.md: 80+ hooks PHP/JS organizados por categoria
  - addon-architecture.md: padrão singleton, compatibilidade, namespaces
  - components-reference.md: dynamic tags, forms, conditions, finder, context menu
  - scripts-styles.md: sistema de assets, frontend handlers
- 9 templates de código prontos:
  - addon-wrapper.php + includes/plugin.php
  - simple-widget.php, advanced-widget.php
  - custom-control.php, dynamic-tag.php
  - form-action.php, form-field.php
  - theme-condition.php, finder-category.php
- 6 configs de agentes: claude, openai, google, codex, antigravity, opencode
- 9 evals com testes de validação + checklist geral
- Script de validação PHP para addons

### Cobertura
- Widgets (criação, registro, remoção, caching, DOM optimization)
- Controles (todos os 35+ tipos, group controls, responsive, wrappers)
- Addons (wrapper completo, compatibilidade, namespaces, best practices)
- Dynamic Tags (criação, grupos, categorias, renderização)
- Form Actions (Elementor Pro - criação, registro, dados)
- Form Fields (Elementor Pro - renderização, validação, injeção de controles)
- Theme Conditions (Elementor Pro - tipos, prioridade, sub-condições)
- Theme Locations (registro, display, migração)
- Finder (categorias, itens, modificação)
- Context Menu (grupos, ações, JS filters)
- Hooks PHP (registro, ciclo de vida, renderização, injeção, formas, recursos)
- Hooks JavaScript (frontend, editor, context menu, forms)
- Scripts & Styles (frontend, editor, preview, widget, control, handlers)
- Deprecations (migração de código, API de depreciação)
- Migração Schemes → Globals (cores e tipografia)

# Elementor Developer Skill - CHANGELOG

## 2.0.0 (2026-04-30)

### Novo: Módulo Completo de CPTs e Itens Personalizados

6 novas referências detalhadas:
- `cpt-taxonomy.md`: Registro de CPTs com suporte a Elementor, taxonomias (hierárquicas/não), CPT Manager, permissões, status customizados, hooks
- `query-widgets.md`: Group_Control_Query, widgets de query com WP_Query, skins (Grid/List/Masonry), tax/meta queries, paginação, caching, estilos CSS
- `custom-tables.md`: Criação de tabelas customizadas (dbDelta), CRUD Repository, meta boxes com tabela customizada, admin pages, custom columns, limpeza de dados
- `cpt-theme-builder.md`: Theme Conditions para CPT (singular, archive, taxonomia, meta field, status), Theme Locations, widgets de info do CPT, breadcrumbs
- `cpt-dynamic-tags.md`: Dynamic tags para post meta, taxonomias, autor, dados de tabelas customizadas (agregações), thumbnail com fallback, integração ACF
- `cpt-controls.md`: Controles customizados (Post Type Select, Taxonomy Select, Post Select com AJAX search, Term Select hierárquico), SELECT2 nativo para CPTs

4 novos templates:
- `cpt-registration.php`: CPT completo com 2 taxonomias (hierárquica + tag) e activation hooks
- `cpt-query-widget.php`: Widget de query com SELECT2 de post types, estilos responsivos, Group_Control_Image_Size
- `cpt-skin.php`: 2 skins (Grid e Lista) para widgets de query
- `cpt-admin-page.php`: Meta box com múltiplos tipos de campo, validação, nonce

4 novos evals:
- `09-cpt-registration.md`: Validação de registro de CPT + taxonomias
- `10-cpt-query-widget.md`: Validação de widget de query com skins
- `11-cpt-theme-condition.md`: Validação de conditions para CPT
- `12-cpt-dynamic-tags.md`: Validação de dynamic tags para CPT

SKILL.md atualizado:
- Descrição e keywords expandidas com CPTs, taxonomias, tabelas customizadas, skins, query widgets
- 3 novas classes base na tabela (Skin_Base, Group_Control_Query, SELECT2)
- Nova seção de hooks para CPTs
- Nova seção "Custom Post Types (CPTs) com Elementor" com exemplos de registro, query, skins, conditions, dynamic tags e controles
- 10 regras específicas para CPTs
- Referências e templates atualizados com links para os novos arquivos

---

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

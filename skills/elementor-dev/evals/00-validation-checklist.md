# Checklist Geral de Validação - Elementor Developer Skill

Use este checklist para validar qualquer código Elementor gerado pela IA.

---

## Widget

- [ ] Estende `\Elementor\Widget_Base`
- [ ] `get_name()` retorna string minúscula, sem espaços
- [ ] `get_title()` usa `esc_html__()`
- [ ] `get_icon()` usa `eicon-*` ou `fa fa-*`
- [ ] `get_categories()` retorna array de strings
- [ ] `get_keywords()` retorna array de strings
- [ ] `has_widget_inner_wrapper()` existe e retorna `false` (widgets novos)
- [ ] `is_dynamic_content()` existe e retorna `false` (widgets estáticos)
- [ ] `register_controls()` define controles em seções com tabs
- [ ] `render()` usa `$this->get_settings_for_display()`
- [ ] `render()` faz early return se valor principal vazio
- [ ] `content_template()` existe e usa sintaxe Lodash
- [ ] Return type declarations em todos os métodos
- [ ] ABSPATH check no topo do arquivo

## Controles

- [ ] Usa `\Elementor\Controls_Manager::TIPO` correto
- [ ] Controles agrupados em seções (`start_controls_section` / `end_controls_section`)
- [ ] Tabs corretas: `TAB_CONTENT` e `TAB_STYLE`
- [ ] Estilos CSS via array `selectors` (não inline CSS no render)
- [ ] `{{WRAPPER}}` usado como prefixo em seletores
- [ ] `{{VALUE}}` para controles de string
- [ ] `{{SIZE}}{{UNIT}}` para sliders
- [ ] Group controls usam `selector` (string), não `selectors` (array)
- [ ] `label_block: true` quando necessário

## Repeater

- [ ] Usa `\Elementor\Controls_Manager::REPEATER`
- [ ] `title_field` definido (ex: `'{{{ titulo }}}'`)
- [ ] PHP render: loop `foreach` com `get_repeater_setting_key()`
- [ ] JS template: `_.each()` com `view.getRepeaterSettingKey()`
- [ ] Inline editing dentro do loop com chave correta

## Links (URL Control)

- [ ] Usa `$this->add_link_attributes( 'key', $settings['url'] )`
- [ ] Usa `$this->print_render_attribute_string( 'key' )`
- [ ] Gera tag `<a>` completa com href, target, rel

## Ícones (ICONS Control)

- [ ] PHP: `\Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] )`
- [ ] Verifica se ícone não está vazio antes de renderizar

## Inline Editing

- [ ] `$this->add_inline_editing_attributes( 'key', 'toolbar' )` no PHP
- [ ] `view.addInlineEditingAttributes( 'key', 'toolbar' )` no JS
- [ ] Toolbars: `none` (TEXT), `basic` (TEXTAREA), `advanced` (WYSIWYG)

## Addon Wrapper

- [ ] Plugin header completo (Name, Description, Version, Author, Text Domain, Requires Plugins)
- [ ] `Elementor tested up to` no header
- [ ] Arquivo principal SEM namespace
- [ ] Função global hookada em `plugins_loaded`
- [ ] Classe principal com Singleton pattern
- [ ] `is_compatible()` com 3 verificações (Elementor loaded, versão Elementor, versão PHP)
- [ ] Admin notices para cada falha
- [ ] `init()` hookado em `elementor/init`
- [ ] Registro de widgets em `elementor/widgets/register`

## Scripts e Estilos

- [ ] Widget scripts: APENAS `wp_register_script()` (NUNCA enqueue direto)
- [ ] Widget styles: APENAS `wp_register_style()` (NUNCA enqueue direto)
- [ ] `get_script_depends()` retorna array de handles registrados
- [ ] `get_style_depends()` retorna array de handles registrados
- [ ] Dependência `elementor-frontend` para scripts com interatividade

## Controle Customizado

- [ ] Estende `\Elementor\Base_Data_Control` (ou `\Elementor\Base_Control`)
- [ ] `get_type()` retorna string única
- [ ] `content_template()` usa Underscore JS
- [ ] `data-setting="{{ data.name }}"` presente (binding obrigatório)
- [ ] `get_control_uid()` para IDs únicos
- [ ] `enqueue()` registra CSS/JS do controle

## Dynamic Tag

- [ ] Estende `\Elementor\Core\DynamicTags\Tag`
- [ ] `get_group()` retorna array de grupo
- [ ] `get_categories()` usa constantes de `Module` (TEXT_CATEGORY, etc.)
- [ ] `render()` faz echo do conteúdo
- [ ] Registro via `elementor/dynamic_tags/register`

## Form Action (Pro)

- [ ] Estende `\ElementorPro\Modules\Forms\Classes\Action_Base`
- [ ] `register_settings_section()` com `condition` em submit_actions
- [ ] `run()` usa `$record->get( 'fields' )` para dados
- [ ] `on_export()` limpa dados sensíveis
- [ ] Registro via `elementor_pro/forms/actions/register`

## Form Field (Pro)

- [ ] Estende `\ElementorPro\Modules\Forms\Fields\Field_Base`
- [ ] `render()` usa `$form->add_render_attribute()`
- [ ] `validation()` usa `$ajax_handler->add_error()`
- [ ] `update_controls()` injeta controles no widget
- [ ] Registro via `elementor_pro/forms/fields/register`

## Theme Condition (Pro)

- [ ] Estende `\ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base`
- [ ] `get_type()` retorna grupo válido (general, archive, singular)
- [ ] `check( $args )` retorna bool
- [ ] Registro via `elementor/theme/register_conditions`

## Segurança

- [ ] ABSPATH check em cada arquivo PHP
- [ ] `esc_html()` ou `esc_attr()` em todo output HTML
- [ ] `esc_html__()` para strings traduzíveis
- [ ] `esc_url()` para URLs
- [ ] `wp_kses_post()` quando necessário

## PHP Moderno

- [ ] Return type declarations (`: string`, `: void`, `: array`, `: bool`)
- [ ] Type hints em parâmetros quando possível
- [ ] Sem tags PHP de fechamento (`?>`) no fim de arquivos PHP

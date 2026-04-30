# Eval: Criar Widget com Repeater

## Prompt
Crie um widget Elementor "Lista de Serviços" com repeater contendo ícone, título, descrição e link. O widget deve ter estilos de tipografia e espaçamento.

## Critérios de Validação

- [ ] Repeater criado com `\Elementor\Controls_Manager::REPEATER`
- [ ] Repeater tem `title_field` definido
- [ ] Campos do repeater: TEXT (título), TEXTAREA (descrição), URL (link), ICONS (ícone)
- [ ] `render()` faz loop sobre `$settings['lista']` (ou nome do repeater) com `foreach`
- [ ] Usa `$this->get_repeater_setting_key( 'campo', 'repeater_name', $index )` dentro do loop
- [ ] Inline editing aplicado em campos de texto dentro do repeater
- [ ] Link usa `$this->add_link_attributes()` + `$this->print_render_attribute_string()`
- [ ] Ícone usa `\Elementor\Icons_Manager::render_icon()`
- [ ] `content_template()` usa `_.each( settings.lista, function( item, index ) { ... } )`
- [ ] JS template usa `view.getRepeaterSettingKey()` para inline editing
- [ ] JS template usa `elementor.helpers.renderIcon()` ou equivalente para ícone
- [ ] Seção STYLE com Group_Control_Typography + SLIDER para espaçamento
- [ ] `selectors` usa `{{SIZE}}{{UNIT}}` para slider
- [ ] `has_widget_inner_wrapper()` = `false`
- [ ] `is_dynamic_content()` = `false`
- [ ] ABSPATH check presente
- [ ] Return type declarations em todos os métodos

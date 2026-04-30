# Eval: Form Action + Form Field

## Prompt
Crie um Form Action "Send to API" que envia dados do formulário via POST para uma URL configurável, e um Form Field "CPF" com validação de formato.

## Critérios de Validação - Form Action

- [ ] Classe estende `\ElementorPro\Modules\Forms\Classes\Action_Base`
- [ ] `get_name()` retorna string única
- [ ] `get_label()` retorna string com `esc_html__()`
- [ ] `register_settings_section()` com controle URL para endpoint + TEXT para campo de mensagem
- [ ] `condition` no controle: `'submit_actions' => $this->get_name()`
- [ ] `run()` acessa dados via `$record->get( 'fields' )` e `$record->get( 'form_settings' )`
- [ ] `run()` usa `wp_remote_post()` com array body
- [ ] `on_export()` faz `unset` das configurações sensíveis (URL)
- [ ] Registro via `elementor_pro/forms/actions/register`
- [ ] Return type declarations

## Critérios de Validação - Form Field

- [ ] Classe estende `\ElementorPro\Modules\Forms\Fields\Field_Base`
- [ ] `get_type()` retorna string única
- [ ] `get_name()` retorna string
- [ ] `render()` usa `$form->add_render_attribute()` + `$form->get_render_attribute_string()`
- [ ] `render()` gera `<input>` com type, class, placeholder
- [ ] `validation()` usa regex para validar CPF (000.000.000-00)
- [ ] `validation()` chama `$ajax_handler->add_error( $field['id'], 'mensagem' )` em falha
- [ ] `update_controls()` injeta controle de placeholder no form widget
- [ ] Registro via `elementor_pro/forms/fields/register`
- [ ] Return type declarations

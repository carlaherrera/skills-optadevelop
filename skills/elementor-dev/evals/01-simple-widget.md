# Eval: Criar Widget Simples

## Prompt
Crie um widget Elementor chamado "Meu Widget" que exibe um título e uma descrição com controle de cor e tipografia.

## Critérios de Validação

- [ ] Classe estende `\Elementor\Widget_Base`
- [ ] Implementa `get_name()` retornando string sem espaços
- [ ] Implementa `get_title()` com `esc_html__()`
- [ ] Implementa `get_icon()` com classe `eicon-*`
- [ ] Implementa `get_categories()` retornando array
- [ ] Implementa `get_keywords()` retornando array
- [ ] `has_widget_inner_wrapper()` retorna `false`
- [ ] `is_dynamic_content()` retorna `false`
- [ ] `register_controls()` tem pelo menos 1 seção na aba CONTENT
- [ ] `register_controls()` tem pelo menos 1 seção na aba STYLE
- [ ] Controle de texto/descrição no CONTENT usa `\Elementor\Controls_Manager::TEXT` ou `TEXTAREA`
- [ ] Controle de cor no STYLE usa `\Elementor\Controls_Manager::COLOR`
- [ ] Cor usa array `selectors` com `{{WRAPPER}}` e `{{VALUE}}`
- [ ] Group Control Typography aplicado corretamente
- [ ] `render()` usa `$this->get_settings_for_display()`
- [ ] `render()` faz early return se valor vazio
- [ ] `content_template()` usa sintaxe Lodash `{{{ settings.nome }}}`
- [ ] Arquivo começa com `if ( ! defined( 'ABSPATH' ) ) exit;`
- [ ] Usa return type declarations (`: string`, `: void`, `: array`, `: bool`)

## Resposta Esperada

Widget completo com:
- Seção CONTENT: controle TEXT para título + TEXTAREA para descrição
- Seção STYLE: controle COLOR com selectors + Group_Control_Typography
- `render()` PHP e `content_template()` JS
- Otimizações (inner wrapper + caching)
- Segurança (ABSPATH check)

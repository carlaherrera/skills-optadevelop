# Eval: Hooks e Renderização

## Prompt
Resolva os seguintes cenários usando hooks Elementor:
1. Remover o widget "Heading" do Elementor
2. Adicionar uma cor customizada ao controle de cor de um widget existente (heading)
3. Modificar o HTML de saída de todos os widgets do tipo "button"

## Critérios de Validação

### Cenário 1 - Remover Widget
- [ ] Usa hook `elementor/widgets/register`
- [ ] Recebe `$widgets_manager` como parâmetro
- [ ] Chama `$widgets_manager->unregister( 'heading' )`
- [ ] Usa `add_action()`

### Cenário 2 - Injetar Controle
- [ ] Usa hook `elementor/element/{stack_name}/{section_id}/before_section_end` ou similar
- [ ] Recebe `$element` e `$args` (ou `$section_id`) como parâmetros
- [ ] Chama `$element->add_control()` na aba TAB_STYLE
- [ ] Controle do tipo COLOR com `selectors` e `{{WRAPPER}}`
- [ ] Usa `add_action()` com priority e 2-3 argumentos aceitos

### Cenário 3 - Modificar Renderização
- [ ] Usa filter `elementor/widget/render_content`
- [ ] Recebe `$content` e `$widget` como parâmetros
- [ ] Verifica `$widget->get_name() === 'button'`
- [ ] Modifica `$content` e retorna
- [ ] Usa `add_filter()` com priority 10 e 2 argumentos aceitos

## Critérios Gerais
- [ ] Todos os exemplos usam `add_action` ou `add_filter` corretamente
- [ ] Todos os exemplos podem ser colocados no arquivo `functions.php` de um tema ou em um plugin
- [ ] Não usa métodos deprecados (ex: `elementor/widgets/widgets_registered`)

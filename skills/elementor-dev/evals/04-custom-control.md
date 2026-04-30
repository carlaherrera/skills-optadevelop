# Eval: Criar Controle Customizado

## Prompt
Crie um controle customizado Elementor chamado "Rating" que exibe 5 estrelas clicáveis para selecionar uma nota de 1 a 5.

## Critérios de Validação

- [ ] Classe estende `\Elementor\Base_Data_Control`
- [ ] `get_type()` retorna string única (ex: 'rating')
- [ ] `get_default_settings()` retorna array com configurações padrão
- [ ] `get_default_value()` retorna valor padrão (ex: '0' ou 0)
- [ ] `enqueue()` registra CSS e JS com `wp_register_*` + `wp_enqueue_*`
- [ ] `content_template()` usa sintaxe Underscore JS
- [ ] `content_template()` usa `$this->get_control_uid()` para IDs únicos
- [ ] `data-setting="{{ data.name }}"` presente no input para binding
- [ ] `data.label` e `data.description` usados no template
- [ ] Exibe `data.value` (valor atual) corretamente
- [ ] Template inclui classes CSS do Elementor (`elementor-control-field`, etc.)
- [ ] Arquivo de registro mostra uso do hook `elementor/controls/register`
- [ ] `$controls_manager->register( new \Rating_Control() )`
- [ ] ABSPATH check presente
- [ ] Return type declarations em todos os métodos

## Resposta Esperada

Controle PHP completo + exemplo de widget usando o controle + CSS para estilizar estrelas.

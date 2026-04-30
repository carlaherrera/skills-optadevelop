# Eval: Criar Dynamic Tag

## Prompt
Crie uma Dynamic Tag Elementor chamada "Post Excerpt" que retorna o excerpt do post atual.

## Critérios de Validação

- [ ] Classe estende `\Elementor\Core\DynamicTags\Tag`
- [ ] `get_name()` retorna string única
- [ ] `get_title()` retorna string com `esc_html__()`
- [ ] `get_group()` retorna array (ex: `['post']`)
- [ ] `get_categories()` retorna array com constante `Module::TEXT_CATEGORY`
- [ ] Usa `use Elementor\Modules\DynamicTags\Module;`
- [ ] `render()` faz echo do conteúdo (usa `get_the_excerpt()`)
- [ ] Arquivo de registro usa hook `elementor/dynamic_tags/register`
- [ ] `$dynamic_tags_manager->register( new \Post_Excerpt_Tag() )`
- [ ] Verifica se `get_queried_object()` existe antes de usar
- [ ] ABSPATH check presente
- [ ] Return type declarations em todos os métodos
- [ ] Textdomain consistente

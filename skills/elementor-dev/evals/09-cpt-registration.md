# Eval 09 - CPT Registration

## Tarefa
Crie um Custom Post Type "Depoimento" com:
- Suporte a Elementor (`elementor` nos supports)
- Taxonomia hierárquica "Tipo de Depoimento"
- Taxonomia não-hierárquica "Tag"
- Menu icon `dashicons-format-quote`
- `show_in_rest` = true
- `has_archive` = true
- `with_front` = false no rewrite

## Checklist de Validação
- [ ] `register_post_type()` chamado no hook `init`
- [ ] `register_taxonomy()` chamado no hook `init`
- [ ] Suporte a Elementor incluído nos supports
- [ ] Textdomain correto em todos os labels
- [ ] `has_archive` = true para Theme Builder
- [ ] `show_in_rest` = true
- [ ] `with_front` = false
- [ ] ABSPATH check no topo do arquivo
- [ ] Namespace correto
- [ ] `flush_rewrite_rules()` na ativação, não em todo request
- [ ] Taxonomia hierárquica com `show_admin_column`
- [ ] Taxonomia não-hierárquica com `show_admin_column`

## Pontos de Atenção
- NUNCA use `flush_rewrite_rules()` em `init` — apenas na ativação/desativação
- Sempre inclua `elementor` nos supports para que o editor funcione
- O slug da taxonomia não pode ter mais de 32 caracteres
- `has_archive` deve ser `true` para que Theme Builder conditions funcionem

---
# Eval 10 - CPT Query Widget

## Tarefa
Crie um widget "CPT Query" que:
- Aceita seleção de post types via SELECT2 múltiplo
- Usa `Group_Control_Query` ou query manual
- Suporta skins (Grid e Lista)
- Mostra: imagem, título (com link), resumo, termos de taxonomia, data
- Controles de grid responsivos (colunas e gap)
- Estilos para card, tipografia, cores
- `has_widget_inner_wrapper()` = false
- `is_dynamic_content()` = false

## Checklist de Validação
- [ ] Widget estende `Widget_Base`
- [ ] `Group_Control_Query` ou query manual implementada
- [ ] Skins registradas no `__construct()`
- [ ] `wp_reset_postdata()` após o loop
- [ ] Controles responsivos para colunas
- [ ] `Group_Control_Image_Size` para imagens
- [ ] `Group_Control_Typography` para títulos/resumos
- [ ] Fallback para quando não há posts
- [ ] NUNCA usa `wp_enqueue_*` para widget assets
- [ ] `get_settings_for_display()` no render (não `get_settings()`)
- [ ] Prefixo no `get_name()`: `meuaddon_cpt_query`

## Pontos de Atenção
- Sempre chame `wp_reset_postdata()` após `WP_Query`
- Use `wp_trim_words()` para limitar resumo
- `has_widget_inner_wrapper()` = false para performance
- Se usar `is_dynamic_content()` = false, o cache é ativado automaticamente
- Use `Group_Control_Image_Size::get_attachment_image_html()` para imagens

---
# Eval 11 - CPT Theme Condition

## Tarefa
Crie uma Theme Condition para CPT "Depoimento":
- Condition singular: verifica `is_singular('depoimento')`
- Condition archive: verifica `is_post_type_archive('depoimento')`
- Condition por taxonomia: verifica `is_tax('tipo_depoimento')`
- Condition por meta field: verifica se `depoimento_destaque` = '1'
- Registre todas no grupo correto (singular/archive)

## Checklist de Validação
- [ ] Estende `Condition_Base`
- [ ] `get_type()` retorna grupo correto (`singular` ou `archive`)
- [ ] `get_name()` retorna string única
- [ ] `get_label()` usa `esc_html__()`
- [ ] `get_priority()` definido (padrão 40)
- [ ] `check()` retorna bool
- [ ] `register_sub_conditions()` para condições filhas
- [ ] Registration no hook `elementor/theme/register_conditions`
- [ ] Adicionada ao grupo correto (`singular` ou `archive`)
- [ ] ABSPATH check

## Pontos de Atenção
- `get_type()` deve retornar `singular` para posts, `archive` para arquivos
- Sub-conditions são registradas dentro de `register_sub_conditions()`
- Use `get_queried_object_id()` para comparar IDs em conditions de termos
- Theme conditions só funcionam com Elementor Pro ativo

---
# Eval 12 - CPT Dynamic Tags

## Tarefa
Crie Dynamic Tags para CPTs:
- Tag "CPT Post Meta": busca qualquer meta key do CPT atual
- Tag "CPT Terms": lista termos de uma taxonomia com links
- Tag "CPT Table Data": dados agregados de tabela customizada (count/sum/avg)
- Registro do grupo "CPT Fields"
- Fallback para valores vazios

## Checklist de Validação
- [ ] Estende `Elementor\Core\DynamicTags\Tag`
- [ ] `get_group()` retorna array com nome do grupo
- [ ] `get_categories()` retorna categorias corretas (text/url/image/number)
- [ ] `register_controls()` para configuração do usuário
- [ ] `render()` usa `echo` para output
- [ ] Fallback implementado para valores vazios
- [ ] `esc_html()`/`esc_url()` em todo output
- [ ] Registration no hook `elementor/dynamic_tags/register`
- [ ] Grupo registrado via `register_group()`
- [ ] ABSPATH check em cada arquivo

## Pontos de Atenção
- Dynamic tags SEMPRE usam `echo` no `render()`
- `get_categories()` deve corresponder ao tipo de dados retornado
- Sempre forneça fallback para quando o valor está vazio
- Use `get_the_ID()` para obter o post ID atual
- Sanitize ALL inputs de controles

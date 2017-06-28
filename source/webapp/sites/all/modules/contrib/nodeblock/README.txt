Nodeblock - Use nodes as blocks

1. Install the Nodeblock module the usual way at admin/build/modules.
2. Edit the content type that you wish to use as a block. In most situations you
   would create a simple content type named Block to use as your custom blocks,
   but you shouldn't feel limited to just this usage.  Any type of node, using
   any type of field may be used as a nodeblock.
3. Select the Enabled radio button on the Available as block field.
4. Create block nodes and watch them populate your block list!


For Omega theme users:
* https://drupal.org/node/1945718

Block caching
Nodeblock tries to determine if your site has special node access functionality
if a module implements hook_node_grants or another module then the `node` module
implements hook_node_access then Nodeblock blocks will not be block cached.

You can override this behavior with variable
"nodeblock_dangerous_force_block_caching" but be aware that this might have
security implications.

//Accessibility fixes for Superfish menu.
//-------------------------------------------
// Common format of the menu elements
//1st level = span.sf-depth-1 OR a.sf-depth-1
//2nd level = span.sf-depth-2 OR a.sf-depth-2
//3nd level = span.sf-depth-3 OR a.sf-depth-3
//4nd level = span.sf-depth-4 OR a.sf-depth-4
//-------------------------------------------
(function (Drupal, once, $) {
  Drupal.behaviors.superfishKeyboardNav = {
    attach: function (context, settings) {
      // Target links inside Superfish menus safely using once().
      once('superfish-keyboard', 'ul.sf-menu a, span.nolink', context).forEach(function (link) {
        link.addEventListener('keydown', function (e) {
          const currentItem = this.closest('li');
          const parentList = currentItem.closest('ul');
          const items = Array.from(parentList.children);
          const index = items.indexOf(currentItem);

          let targetItem = null;

          switch (e.key) {
            case 'ArrowRight':
              // Move to next top-level item to the right side OR.
              // Move to sibling item horizontally to the right side.
              targetItem = items[index];
              e.preventDefault();
              if (targetItem.classList.contains('sf-depth-1')) {
                targetItem = items[index + 1];
                const targetLink = targetItem?.querySelector('a.sf-depth-1') ?? targetItem?.querySelector('span.sf-depth-1');
                if (targetLink) {
                  targetLink.focus();
                }
              }
              else {
                if (targetItem.classList.contains('sf-depth-2')) {
                  const targetLink = targetItem?.querySelector('ul li span.sf-depth-3') ?? targetItem?.querySelector('ul li a.sf-depth-3');
                  if (targetLink) {
                    targetLink.focus();
                  }
                } else {
                  if (targetItem.classList.contains('sf-depth-3')) {
                    const targetLink = targetItem?.querySelector('ul li a.sf-depth-4');
                    if (targetLink) {
                      targetLink.focus();
                    }
                  }
                }
              }
              break;

            case 'ArrowLeft':
              // Move to next top-level item to the left side OR
              // Move to parent item horizontally to the left side
              targetItem = items[index];

              if (targetItem.classList.contains('sf-depth-1')) {
                targetItem = items[index - 1];
                const targetLink = targetItem?.querySelector('a.sf-depth-1') ?? targetItem?.querySelector('span.sf-depth-1');
                if (targetLink) {
                  targetLink.focus();
                }
              }
              else {
                // Level - 2
                if (targetItem.classList.contains('sf-depth-2')) {
                  const targetLink = parentList?.parentElement.querySelector('span.sf-depth-1');
                  if (targetLink) {
                    targetLink.focus();
                  }
                }
                else {
                  if (targetItem.classList.contains('sf-depth-3')) {
                    const targetLink = parentList?.parentElement.querySelector('a.sf-depth-2') ?? parentList?.parentElement.querySelector('span.sf-depth-2');
                    if (targetLink) {
                      targetLink.focus();
                    }
                  }
                  else {
                    if (targetItem.classList.contains('sf-depth-4')) {
                      const targetLink = parentList?.parentElement.querySelector('span.sf-depth-3');
                      if (targetLink) {
                        targetLink.focus();
                      }
                    }
                  }
                }
              }

              break;
            case 'ArrowDown':
              // Navigate through the child menu items towards bottom.
              e.preventDefault();
              targetItem = items[index];
              if (targetItem.classList.contains('sf-depth-1')) {
                const targetLink = targetItem?.querySelector('span.sf-depth-2') ?? targetItem?.querySelector('a.sf-depth-2');
                if (targetLink) {
                  targetLink.focus();
                }
              }
              else {
                targetItem = items[index + 1];
                if (targetItem) {
                  const targetLink = targetItem?.querySelector('a.sf-depth-2');
                  if (targetLink) {
                    targetLink.focus();
                  }
                  else {
                    const targetLink = targetItem?.querySelector('span.sf-depth-3');
                    if (targetLink) {
                      targetLink.focus();
                    }
                    else {
                      const subMenu = currentItem?.querySelector('ul');
                      if (subMenu) {
                        targetItem = subMenu?.querySelector('li');
                      } else {
                        targetItem = items[index + 1];
                      }
                      if (targetItem) {
                        const targetLink = targetItem?.querySelector('a');
                        if (targetLink) {
                          targetLink.focus();
                        }
                      }
                    }
                  }
                }
              }
              break;

            case 'ArrowUp':
              // Navigate through the child menu items towards top.
              // Move to the first level parent.
              // Skip if first level.
              if (items[index].classList.contains('sf-depth-1')) {
                return;
              }
              targetItem = items[index - 1];
              e.preventDefault();

              if (targetItem) {
                const targetLink = targetItem?.querySelector('a.sf-depth-2') ?? targetItem?.querySelector('span.sf-depth-2');
                if (targetLink) {
                  targetLink.focus();
                }
                else {
                  const targetLink = targetItem?.querySelector('span.sf-depth-3');
                  if (targetLink) {
                    targetLink.focus();
                  }
                  else {
                    const subMenu = currentItem?.querySelector('ul');
                    if (subMenu) {
                      targetItem = subMenu?.querySelector('li');
                    } else {
                      targetItem = items[index - 1];
                    }
                    if (targetItem) {
                      const targetLink = targetItem?.querySelector('a');
                      if (targetLink) {
                        targetLink.focus();
                      }
                    }
                  }
                }
              }
              // If the focus is at the first child of second level menu, move to the first level parent.
              if (!targetItem) {
                if (index === 0) {
                  const targetLink = parentList?.parentElement.querySelector('a.sf-depth-1') ?? parentList?.parentElement.querySelector('span.sf-depth-1');
                  if (targetLink) {
                    targetLink.focus();
                  }
                }
              }
              break;
          }
        });
      });
    }
  };

// Fix Superfish roles for WCAG accessibility.
  Drupal.behaviors.removeSuperfishRoles = {
    attach: function (context) {
      once(
        'removeSuperfishRoles',
        '.block-superfish ul, .block-superfish li, .block-superfish a, .block-superfish span.menuparent',
        context
      ).forEach(function (element) {

        const tagName = element.tagName.toLowerCase();

        // Preserve native list semantics.
        if (tagName === 'ul') {
          element.removeAttribute('role');
        }

        // Preserve native list-item semantics.
        else if (tagName === 'li') {
          element.removeAttribute('role');
          element.removeAttribute('tabindex');
        }

        // Preserve native link semantics.
        else if (tagName === 'a') {
          element.removeAttribute('role');
        }

        // Non-link submenu trigger.
        else if (
          tagName === 'span' &&
          element.classList.contains('menuparent')
        ) {
          element.setAttribute('role', 'button');
        }

      });
    }
  };

  // Toggle aria-expanded for Superfish menu upon dropdown opening/closing actions.
  Drupal.behaviors.superfishAriaExpanded = {
    attach: function (context) {
      // Target parent menu items that contain submenus inside Superfish blocks.
      const menuItems = once('superfish-aria-fix', '.block-superfish li.menuparent, .sf-menu li.menuparent', context);

      menuItems.forEach(function (li) {
        const triggerLink = li?.querySelector(':scope > a, :scope > span');
        const subMenu = li?.querySelector(':scope > ul, :scope > div');

        if (!triggerLink || !subMenu) return;

        // Ensure initial state.
        if (!triggerLink.hasAttribute('aria-expanded')) {
          triggerLink.setAttribute('aria-expanded', 'false');
        }
        if (!triggerLink.hasAttribute('aria-haspopup')) {
          triggerLink.setAttribute('aria-haspopup', 'true');
        }

        // Use a MutationObserver to watch for Superfish class toggles (sf-state-open, sfHover).
        const observer = new MutationObserver(function () {
          const isOpen = li?.classList.contains('sf-state-open') || li?.classList.contains('sfHover');
          triggerLink.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        observer.observe(li, {
          attributes: true,
          attributeFilter: ['class']
        });
      });
    }
  };

})(Drupal, once, jQuery) ;

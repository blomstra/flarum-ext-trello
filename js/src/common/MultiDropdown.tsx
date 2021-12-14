import Dropdown from 'flarum/common/components/Dropdown';
import classList from 'flarum/common/utils/classList';
import { IButtonAttrs, default as Button } from 'flarum/common/components/Button';
import type Mithril from 'mithril';

export interface IMultiDropdownItem<T> {
  /**
   * A unique value for each menu item.
   */
  key: string;
  /**
   * A label to display in the menu item.
   */
  label: Mithril.Children;
  /**
   * The value to include in the `onchange` callback's array.
   */
  value: T;
  /**
   * Any other attributes, passed direcly to the {@link Button} element.
   */
  attrs?: Partial<IButtonAttrs>;
}

/**
 * A Dropdown, with the ability to select multiple items at once.
 *
 * ### Attrs
 * - `items`: An array of items to display in the dropdown.
 * - `onchange`: called with array as parameter when selection changes.
 * - `updateOnClose`: only call `onchange` when the dropdown is closed.
 *
 * ### Inherited attrs
 * - `label`: The label to display on the dropdown toggle.
 * - `buttonClassName`: The class to apply to the dropdown toggle (you probably want `"Button"`).
 * - ...and all others on {@link Dropdown}.
 */
export default class MultiDropdown<T> extends Dropdown {
  protected selectedItems: Set<T> = new Set();

  // Only used when `updateOnClose` is true
  protected oldSelectedItems: Set<T> = new Set();

  oncreate(vnode) {
    super.oncreate(vnode);

    if (!this.attrs.updateOnClose) return;

    this.$().on('show.bs.dropdown', () => {
      this.oldSelectedItems = new Set(this.selectedItems);
    });

    this.$().on('hide.bs.dropdown', () => {
      if (
        this.selectedItems.size !== this.oldSelectedItems.size || // Different number of items
        new Set([...this.selectedItems, ...this.oldSelectedItems]).size !== this.selectedItems.size // Items differ
      ) {
        // Changes made
        this.onchange();
      }
    });
  }

  oninit(vnode) {
    super.oninit(vnode);

    this.validateItems();
  }

  getMenu() {
    return (
      <ul className={classList('Dropdown-menu dropdown-menu', this.attrs.menuClassName)}>{this.attrs.items.map((item) => this.getMenuItem(item))}</ul>
    );
  }

  protected onchange() {
    this.attrs.onchange?.(Array.from(this.selectedItems));
  }

  protected validateItems() {
    if (!Array.isArray(this.attrs.items)) {
      throw new Error('[MultiDropdown] You must pass an array of items to a MultiDropdown.');
    }

    if (new Set(this.attrs.items.map((item) => item.key)).size !== this.attrs.items.length) {
      throw new Error('[MultiDropdown] All items must have a unique key.');
    }

    if (new Set(this.attrs.items.map((item) => item.value)).size !== this.attrs.items.length) {
      throw new Error('[MultiDropdown] All items must have a unique value.');
    }
  }

  protected getMenuItem(item: IMultiDropdownItem<T>): Mithril.Children {
    const selected = this.selectedItems.has(item.value);

    const className = classList(item.attrs?.class, item.attrs?.className);

    if (item.attrs?.class) delete item.attrs.class;
    if (item.attrs?.className) delete item.attrs.className;

    return (
      <li key={item.key}>
        <Button
          {...item.attrs}
          class={className}
          icon={selected ? 'fas fa-check' : 'fas'}
          onclick={(e) => {
            // Stop dropdown closing
            e.stopPropagation();

            // Update selected items
            if (selected) {
              this.selectedItems.delete(item.value);
            } else {
              this.selectedItems.add(item.value);
            }

            if (!this.attrs.updateOnClose) {
              this.onchange();
            }
          }}
        >
          {item.label}
        </Button>
      </li>
    );
  }
}

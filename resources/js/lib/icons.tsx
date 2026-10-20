import { isValidElement } from 'react';
import type { ComponentType, ReactNode, SVGProps } from 'react';

/** An icon component reference — a heroicon, a lucide icon or a local glyph. */
export type IconComponent = ComponentType<SVGProps<SVGSVGElement>>;

/**
 * Whether `value` is an icon *component reference* rather than a ready-made
 * node.
 *
 * Testing `typeof value === 'function'` is not enough: `forwardRef`, `memo`
 * and `lazy` all produce plain objects. Heroicons and lucide both ship
 * `{ $$typeof, render }` objects, so a component reference has to be detected
 * by its React tag. Rendering one of those objects as a child is what throws
 * "Objects are not valid as a React child".
 */
export function isIconComponent(value: unknown): value is IconComponent {
    if (typeof value === 'function') {
        return true;
    }

    if (typeof value !== 'object' || value === null || isValidElement(value)) {
        return false;
    }

    return typeof (value as { $$typeof?: unknown }).$$typeof === 'symbol';
}

/**
 * Normalises an `icon` prop that accepts either a component reference or a
 * ready-made node into a renderable node.
 *
 * `className` is applied only when the icon is a component reference; a caller
 * that already built the node controls its own classes.
 */
export function resolveIconNode(
    icon: IconComponent | ReactNode | null | undefined,
    className?: string,
): ReactNode {
    if (icon === null || icon === undefined || icon === false) {
        return null;
    }

    if (isIconComponent(icon)) {
        const Icon = icon;

        return <Icon className={className} />;
    }

    return icon as ReactNode;
}

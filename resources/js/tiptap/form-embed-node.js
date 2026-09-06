import { Node } from '@tiptap/core';

/**
 * Noeud de bloc dédié à l'embed d'un formulaire (spec 14 §4, M8 point 6
 * Pass C4) — premier noeud Tiptap personnalisé du Core. Sérialisé en
 * `<span data-baobab-embed="form:{slug}">`, préservé intact à travers la
 * purification par `MarkerPreservingCleanHtml` (packages/baobab/core), puis
 * résolu en formulaire réellement rendu par le filtre `baobab.richtext.display`
 * à l'affichage.
 */
export default Node.create({
    name: 'formEmbed',
    group: 'inline',
    inline: true,
    atom: true,
    selectable: true,

    addAttributes() {
        return {
            slug: { default: null },
            label: { default: '' },
        };
    },

    parseHTML() {
        return [
            {
                tag: 'span[data-baobab-embed]',
                getAttrs: (element) => {
                    const marker = element.getAttribute('data-baobab-embed') || '';

                    if (! marker.startsWith('form:')) {
                        return false;
                    }

                    return {
                        slug: marker.slice('form:'.length),
                        label: element.textContent || '',
                    };
                },
            },
        ];
    },

    // L'icône n'est jamais dans le texte du noeud : `renderHTML()` sérialise
    // exactement ce que `parseHTML()` relira comme libellé (`textContent`) —
    // un préfixe textuel ici serait recapturé comme faisant partie du
    // libellé à la prochaine ouverture de l'éditeur, puis re-préfixé à
    // nouveau au rendu suivant (icône qui s'accumule à chaque aller-retour,
    // défaut réel trouvé en vérification navigateur). L'icône reste purement
    // visuelle, posée en CSS (`::before` sur `.baobab-form-embed`).
    renderHTML({ node }) {
        return [
            'span',
            {
                'data-baobab-embed': `form:${node.attrs.slug}`,
                contenteditable: 'false',
                class: 'baobab-form-embed',
            },
            node.attrs.label,
        ];
    },
});

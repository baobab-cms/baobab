# Baobab CMS — squelette applicatif

Le point de départ d'un site Baobab : une application Laravel standard qui charge
`baobab/core`, avec les répertoires `modules/` et `themes/`, les configurations
publiées et les entrées d'assets dont l'administration a besoin.

C'est le paquet `baobab/baobab`. Sa **source vit dans le monorepo de développement**
(`packages/baobab/skeleton/`) ; le dépôt public du même nom en sera un miroir produit
par split. Voir la spec 15 §2.

## Installer

**Avec Composer** — la voie du développeur :

```bash
composer create-project baobab/baobab monsite
cd monsite
php artisan baobab:install
```

**Sans Composer** — hébergement mutualisé : télécharger l'archive ZIP pré-packagée
depuis le site officiel (elle contient `vendor/` et les assets déjà compilés),
l'extraire sur le serveur, puis ouvrir `https://votresite.tld/install`.

**[`INSTALL.md`](INSTALL.md) détaille ce second parcours** : versions de PHP,
les deux dispositions possibles selon que la racine du site est choisie ou
imposée, le dépôt des fichiers sans y passer la journée, et les tâches serveur
qui restent à faire.

Dans les deux cas, l'installation passe par la même séquence d'actions (spec 15 §4) :
un seul pipeline, deux interfaces.

## Ce que ce squelette doit contenir, et pourquoi

Trois éléments ne sont pas décoratifs — les retirer casse l'administration :

- **`resources/css/app.css` et `resources/js/app.js`.** Les layouts d'administration
  de `baobab/core` les référencent directement via `@vite`. Le CSS de l'admin, Alpine,
  Tiptap et Cropper vivent donc ici, dans l'application hôte, et non dans le package.
  Un `composer require baobab/core` dans une application Laravel nue donne une
  administration sans style ni interactivité : c'est ce squelette qui rend le contrat
  satisfaisable.
- **`resources/js/api-docs.js`** — même mécanique pour la page de documentation d'API.
- **`database/migrations/0001_01_01_000000_create_users_table.php`.** Le Core *ajoute*
  ses colonnes 2FA à la table `users` ; il ne la crée pas. Elle doit préexister.

## Assets

```bash
npm install
npm run build     # ou `npm run dev` pendant le développement
```

## Derrière un reverse proxy

Si le TLS est terminé en amont (Cloudflare, HAProxy, load balancer d'hébergeur),
décommenter le bloc `trustProxies` de `bootstrap/app.php` **en y renseignant les
adresses réelles du proxy**. Le laisser à `'*'` permettrait à n'importe quel client de
falsifier son adresse IP via `X-Forwarded-For`, que le journal d'audit et le
throttling de Baobab prennent pour argent comptant.

## Développement dans le monorepo

Le `composer.json` déclare `baobab/core` et `baobab/default-theme` en `path`
repositories relatifs (`../core`, `../default-theme`) : le squelette s'installe donc
depuis les sources locales, sans Packagist. Ces dépôts de chemin n'ont aucun sens hors
du monorepo et sont retirés de l'archive publiée (spec 15 §2.1).

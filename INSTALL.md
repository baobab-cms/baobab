# Installer Baobab CMS depuis l'archive pré-packagée

Ce document accompagne l'**archive ZIP pré-packagée**, celle qui contient déjà
ses dépendances et ses fichiers compilés. Vous n'aurez besoin ni de Composer ni
de Node sur le serveur.

> **Deux façons d'installer, au choix.** L'**installateur graphique**
> (`/install`, dans le navigateur) est la voie recommandée sur un hébergement
> mutualisé : déposez l'archive, ouvrez votre domaine, il vous y mène de
> lui-même. Sur un serveur avec accès SSH (VPS, dédié), la **ligne de
> commande** (§3 ci-dessous, `php artisan baobab:install`) va plus vite et
> convient aussi bien. Les deux passent par le même moteur d'installation et
> aboutissent au même site — ce document détaille la voie CLI ; la voie
> graphique se découvre d'elle-même à l'ouverture du domaine.

---

## 1. Vérifier que l'hébergement convient

Une seule exigence bloquante : **PHP 8.4 ou plus récent**.

Chez la plupart des hébergeurs, la version se choisit depuis le panneau
d'administration — *Select PHP Version* ou *MultiPHP Manager* sur cPanel,
*Paramètres PHP* sur Plesk. Pensez à la régler **à la fois** pour le serveur web
et pour la ligne de commande : les deux sont souvent distinctes, et rien ne le
signale.

Si vous vous trompez, Baobab vous le dira clairement plutôt que de rendre une
page blanche : une garde intégrée vérifie la version avant de charger quoi que
ce soit.

Extensions nécessaires : `pdo`, `mbstring`, `intl`, `gd` ou `imagick`, `zip`,
`curl`, `bcmath`. Elles sont présentes par défaut chez la quasi-totalité des
hébergeurs.

---

## 2. Déposer les fichiers

Deux situations, selon ce que votre hébergement autorise.

### Cas A — vous pouvez choisir la racine du site *(le plus simple)*

Extrayez l'archive où vous voulez, puis faites pointer votre domaine sur le
sous-répertoire **`public/`** de l'application.

C'est la disposition recommandée : tout ce qui n'est pas destiné au public —
votre configuration, vos dépendances, vos fichiers envoyés — reste **hors** de
la portée du serveur web.

### Cas B — la racine du site est imposée *(`public_html/`, `www/`…)*

Extrayez l'archive **dans** ce répertoire imposé. Un fichier `.htaccess` livré à
la racine de l'application redirige alors le trafic vers `public/`, où le second
`.htaccess` prend le relais.

C'est prévu et ça fonctionne, mais sachez ce que vous acceptez : dans cette
disposition, vos fichiers d'application se trouvent **sous la racine web**. Ils
ne sont pas servis — les deux `.htaccess` s'en chargent — mais ils le seraient
si le module de réécriture d'Apache venait à être désactivé.

**Ne supprimez ni l'un ni l'autre de ces fichiers.** Ils ne font pas le même
travail : celui de la racine fait *entrer* les requêtes dans `public/`, celui de
`public/` les route vers l'application. Sans le second, seule la page d'accueil
répondrait.

### Déposer par FTP, sans se décourager

L'archive contient environ **15 000 fichiers**. Les envoyer un par un en FTP est
long et fragile — une coupure en milieu de transfert laisse une installation
incomplète, difficile à diagnostiquer.

Préférez, dans cet ordre :

1. **Le gestionnaire de fichiers de votre hébergeur.** Téléversez l'archive
   `.zip` telle quelle — un seul fichier — puis utilisez la fonction
   *Extraire* du panneau. C'est de loin le plus rapide et le plus sûr.
2. **SSH**, si vous y avez accès : `unzip baobab-x.y.z.zip`.
3. **Le FTP fichier par fichier** en dernier recours seulement.

Vérifiez la **somme de contrôle** publiée à côté de l'archive avant de
l'extraire : c'est le seul moyen de savoir que le téléchargement est intact.

---

## 3. Configurer et installer

Depuis la racine de l'application :

```bash
cp .env.example .env
php artisan key:generate
```

Ouvrez ensuite `.env` et renseignez au minimum :

```
APP_URL=https://votredomaine.tld
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=le_nom_de_votre_base
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

Puis :

```bash
php artisan migrate --force
php artisan storage:link
php artisan baobab:super-admin vous@votredomaine.tld
```

`storage:link` n'est pas optionnel : sans lui, **les images envoyées dans la
médiathèque ne s'afficheront pas**. C'est l'oubli le plus fréquent.

Enfin, activez le thème par défaut :

```bash
php artisan module:install baobab/default-theme
php artisan baobab:theme:activate baobab/default-theme
```

---

## 4. Ce qu'il reste à faire côté serveur

**La tâche planifiée.** Baobab en a besoin pour ses envois d'e-mails, ses
purges et ses tâches de fond. Ajoutez-la depuis le panneau de votre hébergeur
(*Cron Jobs* sur cPanel), à raison d'une exécution par minute :

```
* * * * * /usr/bin/php /chemin/vers/votre/site/artisan schedule:run >/dev/null 2>&1
```

Remplacez `/usr/bin/php` par le chemin réel de l'interpréteur PHP 8.4 de votre
hébergement — il est souvent différent de celui par défaut.

**Le traitement des files d'attente.** Les e-mails partent par une file dédiée.
Sur un hébergement mutualisé, la tâche planifiée ci-dessus suffit à les traiter,
à condition que `QUEUE_CONNECTION=database` dans votre `.env`, ce qui est le
réglage livré.

**HTTPS.** Activez le certificat de votre hébergeur et vérifiez que `APP_URL`
commence bien par `https://`.

**Derrière un reverse proxy** (Cloudflare, load balancer d'hébergeur), voyez le
bloc commenté dans `bootstrap/app.php`.

---

## 5. Si quelque chose ne va pas

| Symptôme | Cause la plus fréquente |
|---|---|
| Page « version de PHP insuffisante » | Le serveur sert un PHP antérieur à 8.4 |
| Seule la page d'accueil répond, le reste en 404 | `public/.htaccess` manquant ou réécriture désactivée |
| Erreur 500 immédiate | `.env` absent, ou `APP_KEY` non générée |
| Les images de la médiathèque ne s'affichent pas | `php artisan storage:link` n'a pas été lancé |
| « Aucun thème actif » sur le site public | Le thème par défaut n'a pas été activé (étape 3) |

Pour voir le détail d'une erreur, passez temporairement `APP_DEBUG=true` dans
`.env`, reproduisez le problème, puis **remettez-le à `false`** : cette page
d'erreur détaillée expose votre configuration.

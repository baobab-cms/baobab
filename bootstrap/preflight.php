<?php

/*
 * Garde pré-vol — spec 15 §2.1.
 *
 * Sur un hébergement mutualisé, la version de PHP est précisément ce que
 * l'utilisateur ne choisit pas. Servi par un PHP trop ancien, Baobab ne
 * produirait pas un message d'erreur mais une **erreur d'analyse** dans une
 * dépendance quelconque — c'est-à-dire une page blanche ou un 500 opaque, sans
 * la moindre indication du remède.
 *
 * Ce fichier est chargé en tout premier par `public/index.php` et par
 * `artisan`, avant l'autoloader et avant quoi que ce soit d'autre.
 *
 * ---------------------------------------------------------------------------
 * CONTRAINTE DE RÉDACTION, NON NÉGOCIABLE
 * ---------------------------------------------------------------------------
 * PHP analyse un fichier ENTIER avant d'en exécuter la première ligne. Une
 * garde « PHP 8.4 requis » écrite en syntaxe 8.x meurt donc à l'analyse, avant
 * d'avoir pu afficher son message : elle produit exactement la panne qu'elle
 * prétend éviter.
 *
 * Ce fichier doit rester analysable par un PHP 5. Sont donc interdits ici, et
 * nulle part ailleurs dans le projet : les types de propriétés et de
 * paramètres, les types de retour, `??`, `?->`, `match`, les fonctions
 * fléchées, `declare(strict_types=1)`, les arguments nommés, les constantes
 * de classe typées. Le reste du code de Baobab exige PHP 8.4 et s'en porte
 * bien — ce fichier-ci est la seule exception, et sa raison d'être en dépend.
 */

if (version_compare(PHP_VERSION, '8.4.0', '>=')) {
    return;
}

$baobabRequired = '8.4';
$baobabCurrent = PHP_VERSION;

// En console, un message brut suffit et reste lisible dans un pipe.
if (php_sapi_name() === 'cli') {
    fwrite(STDERR, "\n");
    fwrite(STDERR, '  Baobab CMS exige PHP '.$baobabRequired." ou plus recent.\n");
    fwrite(STDERR, '  Cette machine execute PHP '.$baobabCurrent.".\n");
    fwrite(STDERR, "\n");
    fwrite(STDERR, "  Changez la version de PHP utilisee par la ligne de commande,\n");
    fwrite(STDERR, "  ou appelez l'interpreteur voulu explicitement. Exemple frequent\n");
    fwrite(STDERR, "  en hebergement mutualise :\n");
    fwrite(STDERR, "\n");
    fwrite(STDERR, "      /opt/alt/php84/usr/bin/php artisan ...\n");
    fwrite(STDERR, "\n");
    exit(1);
}

// Le message web est volontairement autonome : ni CSS externe, ni image, ni
// police distante. Il doit s'afficher sur un serveur dont on ne sait rien.
header('HTTP/1.1 500 Internal Server Error');
header('Content-Type: text/html; charset=utf-8');

$safeRequired = htmlspecialchars($baobabRequired, ENT_QUOTES, 'UTF-8');
$safeCurrent = htmlspecialchars($baobabCurrent, ENT_QUOTES, 'UTF-8');

echo '<!doctype html>'."\n";
echo '<html lang="fr"><head><meta charset="utf-8">'."\n";
echo '<meta name="viewport" content="width=device-width, initial-scale=1">'."\n";
echo '<title>Baobab CMS — version de PHP insuffisante</title>'."\n";
echo '<style>'."\n";
echo 'body{margin:0;padding:2.5rem 1.25rem;background:#faf8f5;color:#2e2b24;';
echo 'font:16px/1.6 system-ui,-apple-system,"Segoe UI",Roboto,sans-serif}'."\n";
echo 'main{max-width:38rem;margin:0 auto}'."\n";
echo 'h1{font-size:1.5rem;margin:0 0 1rem;color:#7a3310}'."\n";
echo 'p{margin:0 0 1rem}'."\n";
echo 'code{background:#f0ede7;padding:.15em .4em;border-radius:3px;font-size:.95em}'."\n";
echo 'ul{margin:0 0 1rem;padding-left:1.25rem}'."\n";
echo 'li{margin:0 0 .5rem}'."\n";
echo '.v{font-weight:600}'."\n";
echo '</style></head><body><main>'."\n";

echo '<h1>Cette version de PHP est trop ancienne</h1>'."\n";

echo '<p>Baobab CMS exige <span class="v">PHP '.$safeRequired.'</span> ou plus récent. ';
echo 'Ce serveur exécute <span class="v">PHP '.$safeCurrent.'</span>.</p>'."\n";

echo '<p>Rien n\'est cassé et aucune donnée n\'est perdue : l\'application ne ';
echo 'démarre simplement pas tant que la version de PHP ne convient pas.</p>'."\n";

echo '<p>Chez la plupart des hébergeurs, la version de PHP se change en quelques ';
echo 'clics depuis le panneau d\'administration :</p>'."\n";

echo '<ul>'."\n";
echo '<li><strong>cPanel</strong> — <em>Select PHP Version</em>, ou ';
echo '<em>MultiPHP Manager</em> selon la configuration.</li>'."\n";
echo '<li><strong>Plesk</strong> — <em>Paramètres PHP</em> du domaine.</li>'."\n";
echo '<li><strong>DirectAdmin</strong> — <em>Select PHP Version</em>.</li>'."\n";
echo '</ul>'."\n";

echo '<p>Si votre hébergement ne propose pas PHP '.$safeRequired.', il faudra ';
echo 'demander sa mise à disposition au support, ou changer d\'offre. ';
echo 'PHP '.$safeCurrent.' ne recevra plus de correctifs de sécurité très ';
echo 'longtemps, quel que soit le logiciel que vous y installez.</p>'."\n";

echo '</main></body></html>'."\n";

exit(1);

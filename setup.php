<?php

function setupProject()
{
    echo "Bienvenue dans le script de setup de votre projet PHP MVC.\n";

    // Renommer le dossier du projet
    $currentDir = getcwd();
    $projectName = basename($currentDir);
    echo "Votre projet est nommé : $projectName\n";

    // Initialisation de npm et installation de Vite
    echo "Initialisation de npm et installation de Vite...\n";
    exec("npm init -y");
    exec("npm install vite");

    // Choix du type de CSS
    echo "Choisissez le type de CSS que vous souhaitez utiliser :\n";
    echo "1. CSS natif\n";
    echo "2. SCSS\n";
    echo "3. Bootstrap\n";
    echo "4. TailwindCSS\n";
    echo "Entrez votre choix (1, 2, 3, 4) : ";

    $choice = trim(fgets(STDIN));

    // Création des dossiers si nécessaire
    if (!is_dir('resources/css')) {
        mkdir('resources/css', 0777, true);
    }
    if (!is_dir('resources/scss')) {
        mkdir('resources/scss', 0777, true);
    }

    switch ($choice) {
        case 1:
            echo "Installation de CSS natif...\n";
            file_put_contents('resources/css/app.css', "/* Votre CSS ici */");
            break;
        case 2:
            echo "Installation de SCSS...\n";
            exec("npm install sass");
            file_put_contents('resources/scss/app.scss', "// Votre SCSS ici");
            break;
        case 3:
            echo "Installation de Bootstrap...\n";
            exec("npm install bootstrap");
            file_put_contents('resources/css/app.css', "@import 'bootstrap';");
            break;
        case 4:
            echo "Installation de TailwindCSS...\n";
            exec("npm install -D tailwindcss postcss autoprefixer");
            exec("npx tailwindcss init -p");
            file_put_contents('resources/css/app.css', "@tailwind base;\n@tailwind components;\n@tailwind utilities;");
            file_put_contents('tailwind.config.js', str_replace(
                'content: []',
                'content: ["./app/Views/**/*.php", "./resources/js/**/*.js"]',
                file_get_contents('tailwind.config.js')
            ));
            break;
        default:
            echo "Choix non valide. Par défaut, CSS natif sera utilisé.\n";
            file_put_contents('resources/css/app.css', "/* Votre CSS ici */");
            break;
    }

    // Mise à jour du package.json pour Vite
    $packageJson = json_decode(file_get_contents('package.json'), true);
    $packageJson['scripts'] = [
        'dev' => 'vite',
        'build' => 'vite build'
    ];
    file_put_contents('package.json', json_encode($packageJson, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    // Création des dossiers et fichiers nécessaires pour les vues
    if (!is_dir('app/Views/partials')) {
        mkdir('app/Views/partials', 0777, true);
    }

    if (!file_exists('app/Views/partials/header.php')) {
        file_put_contents('app/Views/partials/header.php', <<<EOL
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo \$title ?? \$_ENV['APP_NAME']; ?></title>
    <?php echo vite(['resources/js/app.js', 'resources/css/app.css']); ?>
</head>
<body>
EOL
        );
    }

    if (!file_exists('app/Views/partials/footer.php')) {
        file_put_contents('app/Views/partials/footer.php', <<<EOL
</body>
</html>
EOL
        );
    }

    echo "Setup terminé. Vous pouvez maintenant commencer à travailler sur votre projet.\n";
}

// Lancement du script de setup
setupProject();

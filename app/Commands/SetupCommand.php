<?php

namespace app\Commands;

use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class SetupCommand extends Command
{
    protected static $defaultName = 'setup';

    protected function configure(): void
    {
        $this->setDescription('Setup your PHP OOP & MVC project');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $output->writeln("Welcome to your new PHP OOP & MVC project setup!");

        $currentDir = getcwd();
        $projectName = basename($currentDir);
        $output->writeln("Your project is named: $projectName");

        $output->writeln("Initialization & installation of Vite...");
        exec("npm init -y");
        exec("npm install vite");

        $question = new ChoiceQuestion(
            'Please pick your CSS flavor (1 - Native CSS, 2 - SCSS, 3 - Bootstrap, 4 - TailwindCSS)',
            ['1', '2', '3', '4'],
            0
        );
        $question->setErrorMessage('%s is not a valid choice.');
        $choice = $helper->ask($input, $output, $question);

        // Remplacement du dossier resources/css
        $this->replaceDirectory();
        mkdir('resources/css', 0777, true);
        mkdir('resources/scss', 0777, true);

        switch ($choice) {
            case '1':
                $output->writeln("Native CSS initialization...");
                file_put_contents('resources/css/app.css', "/* Your CSS goes here */");
                break;
            case '2':
                $output->writeln("SCSS initialization...");
                exec("npm install sass");
                file_put_contents('resources/scss/app.scss', "// Your SCSS goes here");
                break;
            case '3':
                $output->writeln("Bootstrap installation...");
                exec("npm install bootstrap");
                file_put_contents('resources/css/app.css', "@import 'bootstrap';");
                break;
            case '4':
                $output->writeln("TailwindCSS installation...");
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
                $output->writeln("Invalid choice. Defaulting to Native CSS.");
                file_put_contents('resources/css/app.css', "/* Your CSS goes here */");
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

        file_put_contents('app/Views/partials/footer.php', <<<EOL
</body>
</html>
EOL
        );

        $output->writeln("Setup complete. You can now start working on your project.");

        return Command::SUCCESS;
    }

    private function replaceDirectory(): void
    {
        if (is_dir('resources/css')) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator('resources/css', FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileInfo) {
                $todo = ($fileInfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileInfo->getRealPath());
            }
            rmdir('resources/css');
        }
    }

    /**
     * @throws Exception
     */
    public static function launch(): void
    {
        $application = new Application();
        $application->add(new SetupCommand());
        $application->run();
    }
}

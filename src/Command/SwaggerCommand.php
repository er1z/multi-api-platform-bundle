<?php


namespace Er1z\MultiApiPlatform\Command;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Command\SwaggerCommand as SwaggerCommandBase;
use Er1z\MultiApiPlatform\ClassDiscriminator\CliStage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SwaggerCommand extends Command
{

    /**
     * @var SwaggerCommandBase
     */
    private $base;
    /**
     * @var CliStage
     */
    private $stage;
    /**
     * @var array
     */
    private $apis;

    public function __construct(SwaggerCommandBase $base, CliStage $stage, array $apis)
    {
        $this->base = $base;
        $this->stage = $stage;
        $this->apis = $apis;
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('api:swagger:export')
            ->addArgument('api', InputArgument::REQUIRED, sprintf('API to process, available: %s', implode(', ', array_keys($this->apis))))
            ->setDescription('Dump the Swagger 2.0 (OpenAPI) documentation')
            ->addOption('yaml', 'y', InputOption::VALUE_NONE, 'Dump the documentation in YAML')
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Write output to file');
    }

    private function getSubInput(InputInterface $input){
        $optionDefinitions = $this->getDefinition()->getOptions();
        $definition = new InputDefinition(
            $optionDefinitions
        );

        $opts = $input->getOptions();
        $args = [];
        foreach($opts as $k=>$v){
            if($k == 'help'){
                // this is the last option, next ones (stock) are being appended
                break;
            }

            $args['--'.$k] = $v;
        }

        $newInput = new ArrayInput($args, $definition);

        return $newInput;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $input->getArgument('api');

        $newInput = $this->getSubInput($input);

        if($api) {
            $this->stage->activate($api);
        }

        return $this->base->run($newInput, $output);
    }


}
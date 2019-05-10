<?php

namespace App\Organization\Console\Command;

use NumberFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\I18n\Exception\InvalidArgumentException;
use Zend\I18n\Filter\NumberFormat;
use Zend\I18n\Validator\IsFloat;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;

class CreateOrganization extends Command
{
    protected static $defaultName = 'app:create-organization';
    protected $io;
    private $test;

    /**
     * CreateOrganization constructor.
     */
    public function __construct()
    {

        parent::__construct();
    }

    protected function configure()
    {

        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Create a new Organization.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a Organization...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title('Deseja criar uma nova Organização');
        var_dump($this->addEmail());
        return;

        $organization = [
            "organization" => $this->addNameOrganization(),
            "organizationDesc" => $this->addOrganizationDesc(), //Descrição da Organização, area em que atúa
            "firstName" => $this->addName(),
            "lastName" => $this->addLastName(),
            "username" => $this->addUsername(),
            "email" => $this->addEmail(),
            "phone" => $this->addPhone(),
            "country" => $this->addCountry(),
            "language" => $this->addLanguage(),
            "datePay" => $this->addDatePay(),
            "obs" => $this->addObs(),
            "wayPay" => $this->addWayPay(), //Forma de cobrança
            "intervalPay" => $this->addIntervalPay(), //Cobrança periodicidade - mensal, trimestral, semestral, anual.
            "valuePay" => $this->addValuePay(), //Valor do pagamento
            "startPay" => $this->addStartPay(), //Início da cobrança
            "longEvaluation" => $this->addLongEvaluation(), //periodo de avaliação em dias
            "startEvaluation" => $this->addStartEvaluation(), //inicio da avaliação
            "hash" => $this->addHash(),
            "statusContract" => $this->addStatusContract(), //avaliação, suspenso, bloqueado, ativo
            "permission" => $this->addPermission(),

        ];
        print_r($organization);
    }
    private function addNameOrganization() {
     $t  = $this->io->ask('Nome da Organização?', null, function($string) {
            if (strlen($string) < 10) {
                $this->io->note('O nome não satisfaz as exigências, Min. de caracteres 10');
                $this->addNameOrganization();
            }
            return $string;
        });
     return $t;
    }
    private function addOrganizationDesc() {
        return $this->io->choice('Área de trabalho que envolve a organização?', ['Escola de Dança', 'Veterinaria']);
    }
    private function addName(){
        $this->io->ask('Usuário nome?', null, function ($string) {
            if (strlen($string) < 5) {
                $this->io->note('O nome não satisfaz as exigências, Min. de caracteres 5');
                $this->addName();
            }
            return (string) $string;
        });
    }
    private function addLastName(){
        $this->io->ask('Usuário sobrenome?', null, function ($string) {
            if (strlen($string) < 5) {
                $this->io->note('O nome não satisfaz as exigências, Min. de caracteres 5');
                $this->addName();
            }
            return (string) $string;
        });
    }
    private function addUsername(){
       return $this->io->ask('Usuário username?', null, function ($value) {
            if (strlen($value) < 6) {
                $this->io->warning('O nome não satisfaz as exigências, Min. de caracteres 6');
                $this->addName();
            }
            return (string) $value;
        });
    }

    private function addEmail(){
     return   $this->io->ask('E-mail do usuário?', null, function ($value) {

            $validator = new EmailAddress();
            $validator->setOptions(array('domain' => false));
            if (!$validator->isValid($value)){
                $this->io->warning(' A String passada não corresponde a um e-mail válido.');
                $this->addEmail();
            }

            $validatorLen = new StringLength(array('max' => 50));
            if (!$validatorLen->isValid($value)){
                $this->io->warning(' A string passada não corresponde as exigências, não pode ser maior do que 50 caracteres');
                $this->addEmail();
            }
            return $value;
        });
    }
    private function addPhone(){
        $ar["phone"] = $this->filters($this->io->ask('Phone ?', null));
        $ar["pref"] = $this->filters($this->io->ask('Prefixo do País ?', null));
        $ar["ddd"] = $this->filters($this->io->ask('DDD Cidade ?', null));
        return $ar;
    }
    private function addCountry(){
       return $this->filters($this->io->ask('País?', null));
    }
    private function addLanguage(){
      return  $this->io->choice('Linguagem ?', ['Português', 'Ingles'], "Ingles");
    }
    private function addDatePay(){
        return $this->io->choice('Data para efetuar o pagamento ?', ['1', '5', "10", "20"], "5");
    }
    private function addObs(){
        return $this->filters($this->io->ask('Adicionar OBS ', null));
    }
    private function addWayPay(){
        return $this->filters($this->io->ask('Forma de Pagamento ', "Paypal"));
    }
    private function addIntervalPay(){
        return $this->io->choice('Periodicidade ', ['Mensal', 'Trimensal', "Semestral", "Anual"], "Mensal");
    }
    private function addValuePay(){
    return  $this->io->ask('Valor Combinado', null, function ($number){
            $validator = new isFloat();
            if(!$validator->isValid($number)){
                throw new InvalidArgumentException("O valor tem de ser um digito inteiro ou decimal");
                $this->addValuePay();
            };
            return $number;
        });
    }
    private function addStartPay(){
      return  $this->io->ask('Number of workers to start', 1, function ($number){
            if (!is_numeric($number)) {
                throw new \RuntimeException('You must type a number.');
            }
            return  $number;
        });
    }
    private function addLongEvaluation(){
        return $this->io->choice('Tempo de avaliação em dias ?', [0, 3, 7, 10, 15, 30], 7);
    }
    private function addStartEvaluation(){
        return $this->io->choice('Inicío da Avaliaço ?', [new \DateTime("now")]);
    }
    private function filters($value){
        $stripTags = new StripTags();
        $value = $stripTags->filter($value);

        $stringTrim = new StringTrim();
        return $stringTrim->filter($value);
    }
}
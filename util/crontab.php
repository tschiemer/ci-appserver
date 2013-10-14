<?php


$options = getopt('abc::d:',array());

//if (empty($options))
//{
//    echo "Usage: {$argv[0]}\n";
//    return;
//}

//var_dump($options);


require_once dirname(__FILE__). '/../www/application/third_party/crontab.php';

$crontab = new Crontab_Manager();//array('script-cmd'=>'crontab.php'));


if ( ! $crontab->load_jobs())
{
//    $crontab->add_job('say hello','* * * * *')->commit();
    die("Could not read crontab!\n");
}

if(count($crontab->_jobs['my']))
{
    echo 'my.Jobs before = ' . implode(', ', array_keys($crontab->_jobs['my'])) . "\n";
}
else
{
    echo "my.Jobs before\n";
}


return;

$crontab->add_job('say hello', '* * * * *',array(
    'job-id' => 'foohello',
    'once'   => true
));

if(count($crontab->_jobs['my']))
{
    echo 'my.Jobs add = ' . implode(', ', array_keys($crontab->_jobs['my'])) . "\n";
}
else
{
    echo "my.Jobs add\n";
}
//print_r($crontab->_jobs);
$crontab->commit();

if (! $crontab->load_jobs())
{
    die("could not read crontab file!\n");
}

if(count($crontab->_jobs['my']))
{
    echo 'my.Jobs after = ' . implode(', ', array_keys($crontab->_jobs['my'])) . "\n";
}
else
{
    echo "my.Jobs after\n";
}
//print_r($crontab->_other_jobs);

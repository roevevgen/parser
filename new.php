<?php

// Використовуємо функцію shell_exec для виконання команди wmic bios get smbiosbiosversion
    $command = 'wmic bios get smbiosbiosversion';
    $output = shell_exec($command);

// Виводимо результат на екран
    echo 'Версія BIOS на вашому ноутбуці Dell Inspiron N7110:' . PHP_EOL;
    echo $output;


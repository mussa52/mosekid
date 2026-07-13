<?php
declare(strict_types=1);

namespace Controller;

class PayrollController
{
    public function index(): void
    {
        $_SESSION['error'] = 'Payroll is currently unavailable.';
        redirect('/index.php?action=dashboard');
    }

    public function stats(): void
    {
        $_SESSION['error'] = 'Payroll stats are currently unavailable.';
        redirect('/index.php?action=dashboard');
    }

    public function monthly(): void
    {
        $_SESSION['error'] = 'Payroll monthly is currently unavailable.';
        redirect('/index.php?action=dashboard');
    }

    public function payslips(): void
    {
        $_SESSION['error'] = 'Payroll payslips are currently unavailable.';
        redirect('/index.php?action=dashboard');
    }

    public function generate(): void
    {
        $_SESSION['error'] = 'Payroll generation is currently unavailable.';
        redirect('/index.php?action=dashboard');
    }

    public function process(): void
    {
        $_SESSION['error'] = 'Payroll processing is currently unavailable.';
        redirect('/index.php?action=dashboard');
    }
}

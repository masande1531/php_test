<?php
namespace Clickatell;

use \InvalidArgumentException;
use \Exception;
use \PDO;

class CompanyService
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    /**
     * Locate all the users in a company
     */
    public function locateCompanyUsers($companyId)
    {
        $stmt = $this->database->query('SELECT * FROM companies WHERE `company_id`=' . $companyId);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $company = $stmt->fetch();

        if (empty($company)) {
            throw new InvalidArgumentException('Company could not be found.');
        }

        $stmt = $this->database->query('SELECT * FROM users WHERE company_id=' . $companyId);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $users = $stmt->fetchAll();

        return $users;
    }

    /**
     * Delete all the users belonging to a specific company
     */
    public function deleteCompanyUsers($companyId)
    {
        $stmt = $this->database->query('SELECT * FROM companies WHERE company_id=' . $companyId);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $company = $stmt->fetch();

        if (empty($company)) {
            throw new InvalidArgumentException('Company could not be found.');
        }

        return $this->database->exec('DELETE FROM users WHERE company_id=' . $companyId);
    }

    /**
     * Link a specific user ID to a company ID
     */
    public function linkCompanyUser($userId, $companyId)
    {
        $stmt = $this->database->query('SELECT * FROM companies WHERE company_id=' . $companyId);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $company = $stmt->fetch();

        if (empty($company)) {
            throw new InvalidArgumentException('Company could not be found.');
        }

        $stmt = $this->database->query('SELECT * FROM users WHERE user_id=' . $userId);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $user = $stmt->fetch();

        if (empty($user)) {
            throw new InvalidArgumentException('User could not be found.');
        }

        return $this->database->exec('UPDATE users SET company_id=' . $companyId . ' WHERE user_id=' . $userId);
    }

    /**
     * Broadcast the worth of a user and return the result as
     * an array
     */
    public function userWorth($companyId)
    {
        $stmt = $this->database->query('SELECT * FROM users WHERE company_id=' . $companyId);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $users = $stmt->fetchAll();
        $result = array();

        foreach ($users as $user)
        {
            switch ($user['user_mtype'])
            {
                case 3:
                    $result[] = $user['user_name'] . ': Worth means nothing to me.';
                    break;
                case 2:
                    $result[] = $user['user_name'] . ': I am worth billions.';
                    break;
                default:
                    $result[] = $user['user_name'] . ': I am not worth much.';
                    break;
            }
        }

        return $result;
    }
}
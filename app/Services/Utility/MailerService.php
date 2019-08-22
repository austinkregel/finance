<?php
/**
 * MailerService
 */
declare(strict_types=1);

namespace App\Services\Utility;

use App\Models\User;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Mail\Mailer;

/**
 * Class MailerService
 * @package App\Services\Utility
 */
class MailerService
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var User[]
     */
    protected $users;

    /**
     * MailerService constructor.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send the mail off
     * @param Mailable $mailable
     * @return MailerService
     */
    public function send(Mailable $mailable): self
    {
        if (empty($this->users)) {
            throw new \DomainException('You must pass an array of users');
        }

        $this->mailer->to($this->users)
            ->send($mailable);

        return $this;
    }

    /**
     * Who to send to.
     * @param string[]|string|User|User[] $users
     * @return MailerService
     */
    public function to($users): self
    {
        if (!is_array($users)) {
            $users = [$users];
        }

        $this->users = $users;

        return $this;
    }
}

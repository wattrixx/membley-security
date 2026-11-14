<?php

use App\Libraries\PublicSubmissionTokens;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class PublicSubmissionTokensTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        service('session')->remove('public_submission_tokens');
    }

    public function testTokensAreBoundToAFormAndConsumedOnce(): void
    {
        $tokens = new PublicSubmissionTokens();
        $token = $tokens->issue('application');

        $this->assertMatchesRegularExpression('/\A[a-f0-9]{64}\z/', $token);
        $this->assertTrue($tokens->isValid('application', $token));
        $this->assertFalse($tokens->isValid('college_enquiry', $token));

        $tokens->consume($token);

        $this->assertFalse($tokens->isValid('application', $token));
    }

    public function testMalformedTokensAreRejected(): void
    {
        $this->assertFalse((new PublicSubmissionTokens())->isValid('application', 'not-a-token'));
    }

    public function testThrottlerRejectsRequestsAboveCapacity(): void
    {
        $key = 'milestone6-test-' . bin2hex(random_bytes(8));
        $throttler = service('throttler');

        $this->assertTrue($throttler->check($key, 2, 60));
        $this->assertTrue($throttler->check($key, 2, 60));
        $this->assertFalse($throttler->check($key, 2, 60));
    }
}

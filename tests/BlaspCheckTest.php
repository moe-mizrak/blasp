<?php

namespace Blaspsoft\Blasp\Tests;

use Blaspsoft\Blasp\BlaspService;
use Blaspsoft\Blasp\Tests\TestCase;
use Illuminate\Support\Facades\Config;

class BlaspCheckTests extends TestCase
{
    protected $blaspService;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('blasp.profanities', ['fucking', 'shit', 'cunt', 'fuck', 'penis', 'cock', 'twat', 'ass', 'dick', 'sex', 'butt', 'arse', 'lick', 'anal', 'clusterfuck', 'bullshit', 'fucked', 'damn', 'crap', 'hell']);
        Config::set('blasp.separators', [' ', '-', '_']);
        Config::set('blasp.substitutions', [
            '/a/' => ['a', '4', '@', 'Á', 'á', 'À', 'Â', 'à', 'Â', 'â', 'Ä', 'ä', 'Ã', 'ã', 'Å', 'å', 'æ', 'Æ', 'α', 'Δ', 'Λ', 'λ'],
            '/b/' => ['b', '8', '\\', '3', 'ß', 'Β', 'β'],
            '/c/' => ['c', 'Ç', 'ç', 'ć', 'Ć', 'č', 'Č', '¢', '€', '<', '(', '{', '©'],
            '/d/' => ['d', '\\', ')', 'Þ', 'þ', 'Ð', 'ð'],
            '/e/' => ['e', '3', '€', 'È', 'è', 'É', 'é', 'Ê', 'ê', 'ë', 'Ë', 'ē', 'Ē', 'ė', 'Ė', 'ę', 'Ę', '∑'],
            '/f/' => ['f', 'ƒ'],
            '/g/' => ['g', '6', '9'],
            '/h/' => ['h', 'Η'],
            '/i/' => ['i', '!', '|', ']', '[', '1', '∫', 'Ì', 'Í', 'Î', 'Ï', 'ì', 'í', 'î', 'ï', 'ī', 'Ī', 'į', 'Į'],
            '/j/' => ['j'],
            '/k/' => ['k', 'Κ', 'κ'],
            '/l/' => ['l', '!', '|', ']', '[', '£', '∫', 'Ì', 'Í', 'Î', 'Ï', 'ł', 'Ł'],
            '/m/' => ['m'],
            '/n/' => ['n', 'η', 'Ν', 'Π', 'ñ', 'Ñ', 'ń', 'Ń'],
            '/o/' => ['o', '0', 'Ο', 'ο', 'Φ', '¤', '°', 'ø', 'ô', 'Ô', 'ö', 'Ö', 'ò', 'Ò', 'ó', 'Ó', 'œ', 'Œ', 'ø', 'Ø', 'ō', 'Ō', 'õ', 'Õ'],
            '/p/' => ['p', 'ρ', 'Ρ', '¶', 'þ'],
            '/q/' => ['q'],
            '/r/' => ['r', '®'],
            '/s/' => ['s', '5', '$', '§', 'ß', 'Ś', 'ś', 'Š', 'š'],
            '/t/' => ['t', 'Τ', 'τ'],
            '/u/' => ['u', 'υ', 'µ', 'û', 'ü', 'ù', 'ú', 'ū', 'Û', 'Ü', 'Ù', 'Ú', 'Ū'],
            '/v/' => ['v', 'υ', 'ν'],
            '/w/' => ['w', 'ω', 'ψ', 'Ψ'],
            '/x/' => ['x', 'Χ', 'χ'],
            '/y/' => ['y', '¥', 'γ', 'ÿ', 'ý', 'Ÿ', 'Ý'],
            '/z/' => ['z', 'Ζ', 'ž', 'Ž', 'ź', 'Ź', 'ż', 'Ż'],
  
        ]);
    }

    public function test_real_blasp_service()
    {
        $blaspService = new BlaspService();
        
        $result = $blaspService->check('This is a fuck!ng sentence');
        
        $this->assertTrue($result->hasProfanity);
    }

    public function test_straight_match()
    {
        $blaspService = new BlaspService();
        
        $result = $blaspService->check('This is a fucking sentence');
    
        $this->assertTrue($result->hasProfanity);
        $this->assertSame(1, $result->profanitiesCount);
        $this->assertCount(1, $result->uniqueProfanitiesFound);
        $this->assertSame('This is a ******* sentence', $result->cleanString);
    }

    public function test_substitution_match()
    {
        $blaspService = new BlaspService();
        
        $result = $blaspService->check('This is a fÛck!ng sentence');

        $this->assertTrue($result->hasProfanity);
        $this->assertSame(1, $result->profanitiesCount);
        $this->assertCount(1, $result->uniqueProfanitiesFound);
        $this->assertSame('This is a ******* sentence', $result->cleanString);
    }

    public function test_obscured_match()
    {
        $blaspService = new BlaspService();
        
        $result = $blaspService->check('This is a f-u-c-k-i-n-g sentence');

        $this->assertTrue($result->hasProfanity);
        $this->assertSame(1, $result->profanitiesCount);
        $this->assertCount(1, $result->uniqueProfanitiesFound);
        $this->assertSame('This is a ************* sentence', $result->cleanString);
    }

    public function test_doubled_match()
    {
        $blaspService = new BlaspService();
        
        $result = $blaspService->check('This is a ffuucckkiinngg sentence');

        $this->assertTrue($result->hasProfanity);
        $this->assertSame(1, $result->profanitiesCount);
        $this->assertCount(1, $result->uniqueProfanitiesFound);
        $this->assertSame('This is a ************** sentence', $result->cleanString);
    }

    public function test_combination_match()
    {
        $blaspService = new BlaspService();
        
        $result = $blaspService->check('This is a f-uuck!ng sentence');

        $this->assertTrue($result->hasProfanity);
        $this->assertSame(1, $result->profanitiesCount);
        $this->assertCount(1, $result->uniqueProfanitiesFound);
        $this->assertSame('This is a ********* sentence', $result->cleanString);
    }

    public function test_multiple_profanities_no_spaces()
    {
        $blaspService = new BlaspService();
        
        $result = $blaspService->check('cuntfuck shit');

        $this->assertTrue($result->hasProfanity);
        $this->assertSame(3, $result->profanitiesCount);
        $this->assertCount(3, $result->uniqueProfanitiesFound);
        $this->assertSame('******** ****', $result->cleanString);
    }

    public function test_multiple_profanities()
    {
        $blaspService = new BlaspService();
        
        $result = $blaspService->check('This is a fuuckking sentence you fucking cunt!');

        $this->assertTrue($result->hasProfanity);
        $this->assertSame(3, $result->profanitiesCount);
        $this->assertCount(2, $result->uniqueProfanitiesFound);
        $this->assertSame('This is a ********* sentence you ******* ****!', $result->cleanString);
    }

    public function test_scunthorpe_problem()
    {
        $blaspService = new BlaspService();
        
        $result = $blaspService->check('I live in a town called Scunthorpe');

        $this->assertTrue(!$result->hasProfanity);
        $this->assertSame(0, $result->profanitiesCount);
        $this->assertCount(0, $result->uniqueProfanitiesFound);
        $this->assertSame('I live in a town called Scunthorpe', $result->cleanString);
    }

    public function test_penistone_problem()
    {
        $blaspService = new BlaspService();
        
        $result = $blaspService->check('I live in a town called Penistone');

        $this->assertTrue(!$result->hasProfanity);
        $this->assertSame(0, $result->profanitiesCount);
        $this->assertCount(0, $result->uniqueProfanitiesFound);
        $this->assertSame('I live in a town called Penistone', $result->cleanString);
    }

    public function test_false_positives()
    {
        $words = [
            'Scunthorpe',
            'Cockburn',
            'Penistone',
            'Lightwater',
            'Assume',
            'bass',
            'class',
            'Compass',
            'Pass',
            'Dickinson',
            'Middlesex',
            'Cockerel',
            'Butterscotch',
            'Blackcock',
            'Countryside',
            'Arsenal',
            'Flick',
            'Flicker',
            'Analyst',
            'blackCocktail',
        ];

        foreach ($words as $word) {

            $blaspService = new BlaspService();
            
            $result = $blaspService->check($word);

            $this->assertTrue(!$result->hasProfanity);
            $this->assertSame(0, $result->profanitiesCount);
            $this->assertCount(0, $result->uniqueProfanitiesFound);
            $this->assertSame($word, $result->cleanString);       
        }
    }

    public function test_cuntfuck_fuckcunt()
    {
        $blaspService = new BlaspService();
        
        $result = $blaspService->check('cuntfuck fuckcunt');

        $this->assertTrue($result->hasProfanity);
        $this->assertSame(4, $result->profanitiesCount);
        $this->assertCount(2, $result->uniqueProfanitiesFound);
        $this->assertSame('******** ********', $result->cleanString);
    }

    public function test_fucking_shit_cunt_fuck()
    {
        $blaspService = new BlaspService();
        
        $result = $blaspService->check('fuckingshitcuntfuck');

        $this->assertTrue($result->hasProfanity);
        $this->assertSame(4, $result->profanitiesCount);
        $this->assertCount(4, $result->uniqueProfanitiesFound);
        $this->assertSame('*******************', $result->cleanString);
    }

    public function test_billy_butcher()
    {
        $blaspService = new BlaspService();
        
        $result = $blaspService->check('oi! cunt!');

        $this->assertTrue($result->hasProfanity);
        $this->assertSame(1, $result->profanitiesCount);
        $this->assertCount(1, $result->uniqueProfanitiesFound);
        $this->assertSame('oi! ****!', $result->cleanString);
    }

    public function test_paragraph()
    {
        $blaspService = new BlaspService();

        $paragraph = "This damn project is such a pain in the ass. I can't believe I have to deal with this bullshit every single day. It's like everything is completely fucked up, and nobody gives a shit. Sometimes I just want to scream, 'What the hell is going on?' Honestly, it's a total clusterfuck, and I'm so fucking done with this crap.";
        
        $result = $blaspService->check($paragraph);

        $expectedOutcome = "This **** project is such a pain in the ***. I can't believe I have to deal with this ******** every single day. It's like everything is completely ****** up, and nobody gives a ****. Sometimes I just want to scream, 'What the **** is going on?' Honestly, it's a total ***********, and I'm so ******* done with this ****.";

        $this->assertTrue($result->hasProfanity);
        $this->assertSame(9, $result->profanitiesCount);
        $this->assertCount(9, $result->uniqueProfanitiesFound);
        $this->assertSame($expectedOutcome, $result->cleanString);
    }
}
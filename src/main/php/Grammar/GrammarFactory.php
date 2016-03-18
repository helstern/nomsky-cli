<?php namespace Helstern\Nomsky\Grammar;

use Helstern\Nomsky\Grammars\Ebnf\Grammar;
use Helstern\Nomsky\Grammars\Ebnf\IsoEbnf;
use Helstern\Nomsky\Parser\Errors\ParseAssertions;
use Helstern\Nomsky\Tokens\TokenPredicates;

class GrammarFactory
{
    /** @var IsoEbnf\LexerFactory */
    private $ebnfLexerFactory;

    /**
     * @param IsoEbnf\LexerFactory $ebnfLexerFactory
     */
    public function __construct(IsoEbnf\LexerFactory $ebnfLexerFactory = null)
    {
        if (is_null($ebnfLexerFactory)) {
            $ebnfLexerFactory = new IsoEbnf\LexerFactory();
        }
        $this->ebnfLexerFactory = $ebnfLexerFactory;
    }

    /**
     * @param string $path
     *
     * @return \Helstern\Nomsky\Grammar\StandardGrammar
     */
    public function ebnfFromFile($path)
    {
        $lexer = $this->ebnfLexerFactory->fromFile($path);

        $assertions = new ParseAssertions(new TokenPredicates);
        $parser = new IsoEbnf\Parser($assertions);

        $ast = $parser->parse($lexer);

        $translator = new Grammar\AstTranslator();
        $grammar = $translator->translate($ast);
        return $grammar;
    }
}

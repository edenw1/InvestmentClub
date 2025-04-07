<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* controller.php.twig */
class __TwigTemplate_31cefdcc31ef8680f24597b011bdcbcf extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'header' => [$this, 'block_header'],
            'subheader' => [$this, 'block_subheader'],
            'content1' => [$this, 'block_content1'],
            'content2' => [$this, 'block_content2'],
            'content3' => [$this, 'block_content3'],
            'footer' => [$this, 'block_footer'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html>
<head>
    <title>";
        // line 4
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>
    <!-- Link to the external CSS file -->
    <link rel=\"stylesheet\" href=\"css/homePageStyles.css\">
</head>
<body>
    <div class=\"sidebar\">
        <img src=\"Inv_Logo.png\" alt=\"Investment Club Logo\">
    </div>
    <div class=\"content\">
        <header>
            <h1>";
        // line 14
        yield from $this->unwrap()->yieldBlock('header', $context, $blocks);
        yield "</h1>
        </header>
        <nav>
            <a href=\"#\">Home</a>
            <a href=\"#\">Transaction</a>
            <a href=\"#\">About</a>
            <a href=\"#\">Presentations</a>
        </nav>
        <div class=\"container\">
            <h2>";
        // line 23
        yield from $this->unwrap()->yieldBlock('subheader', $context, $blocks);
        yield "</h2>

            ";
        // line 25
        if ( !CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "is_authenticated", [], "any", false, false, false, 25)) {
            // line 26
            yield "                <form action=\"login.php\" method=\"post\">
                    <input type=\"submit\" value=\"Login\">
                </form>
            ";
        } else {
            // line 30
            yield "                <form action=\"transactions.php\" method=\"post\">
                    <input type=\"submit\" value=\"See Transactions\">
                </form>
                <form action=\"presentations.php\" method=\"post\">
                    <input type=\"submit\" value=\"Presentations\">
                </form>
                ";
            // line 36
            if (CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "is_admin", [], "any", false, false, false, 36)) {
                // line 37
                yield "                    <form action=\"adminPage.php\" method=\"post\">
                        <input type=\"submit\" value=\"Admin Panel\">
                    </form>
                ";
            }
            // line 41
            yield "                <form action=\"logout.php\" method=\"post\">
                    <input type=\"submit\" value=\"Logout\">
                </form>
            ";
        }
        // line 45
        yield "
            <div class=\"stock-card\">
                ";
        // line 47
        yield from $this->unwrap()->yieldBlock('content1', $context, $blocks);
        // line 48
        yield "            </div>
            <div class=\"stock-card\">
                ";
        // line 50
        yield from $this->unwrap()->yieldBlock('content2', $context, $blocks);
        // line 51
        yield "            </div>
            <div class=\"stock-card\">
                ";
        // line 53
        yield from $this->unwrap()->yieldBlock('content3', $context, $blocks);
        // line 54
        yield "            </div>
        </div>
        <footer>
            <p>";
        // line 57
        yield from $this->unwrap()->yieldBlock('footer', $context, $blocks);
        yield "</p>
        </footer>
    </div>
</body>
</html>
";
        yield from [];
    }

    // line 4
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield "Stock Website";
        yield from [];
    }

    // line 14
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_header(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield "Investment Club";
        yield from [];
    }

    // line 23
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_subheader(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield "Welcome, Investment Club!";
        yield from [];
    }

    // line 47
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content1(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    // line 50
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content2(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    // line 53
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content3(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    // line 57
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_footer(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield "&copy; 2025 Stock Tracker. All rights reserved.";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "controller.php.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  211 => 57,  201 => 53,  191 => 50,  181 => 47,  170 => 23,  159 => 14,  148 => 4,  137 => 57,  132 => 54,  130 => 53,  126 => 51,  124 => 50,  120 => 48,  118 => 47,  114 => 45,  108 => 41,  102 => 37,  100 => 36,  92 => 30,  86 => 26,  84 => 25,  79 => 23,  67 => 14,  54 => 4,  49 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "controller.php.twig", "C:\\Users\\anhtr\\OneDrive\\Desktop\\UniServerZ\\www\\InvestmentClub\\templates\\controller.php.twig");
    }
}

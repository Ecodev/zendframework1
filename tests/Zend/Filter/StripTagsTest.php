<?php
/**
 * Zend Framework.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version    $Id$
 */

// Call Zend_Filter_StripTagsTest::main() if this source file is executed directly.

/**
 * @see Zend_Filter_StripTags
 */
require_once 'Zend/Filter/StripTags.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Filter
 */
#[AllowDynamicProperties]
class Zend_Filter_StripTagsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Zend_Filter_StripTags object.
     *
     * @var Zend_Filter_StripTags
     */
    protected $_filter;

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite(self::class);
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Creates a new Zend_Filter_StripTags object for each test method.
     */
    public function setUp(): void
    {
        $this->_filter = new Zend_Filter_StripTags();
    }

    /**
     * Ensures that getTagsAllowed() returns expected default value.
     */
    public function testGetTagsAllowed()
    {
        $this->assertEquals([], $this->_filter->getTagsAllowed());
    }

    /**
     * Ensures that setTagsAllowed() follows expected behavior when provided a single tag.
     */
    public function testSetTagsAllowedString()
    {
        $this->_filter->setTagsAllowed('b');
        $this->assertEquals(['b' => []], $this->_filter->getTagsAllowed());
    }

    /**
     * Ensures that setTagsAllowed() follows expected behavior when provided an array of tags.
     */
    public function testSetTagsAllowedArray()
    {
        $tagsAllowed = [
            'b',
            'a' => 'href',
            'div' => ['id', 'class'],
        ];
        $this->_filter->setTagsAllowed($tagsAllowed);
        $tagsAllowedExpected = [
            'b' => [],
            'a' => ['href' => null],
            'div' => ['id' => null, 'class' => null],
        ];
        $this->assertEquals($tagsAllowedExpected, $this->_filter->getTagsAllowed());
    }

    /**
     * Ensures that getAttributesAllowed() returns expected default value.
     */
    public function testGetAttributesAllowed()
    {
        $this->assertEquals([], $this->_filter->getAttributesAllowed());
    }

    /**
     * Ensures that setAttributesAllowed() follows expected behavior when provided a single attribute.
     */
    public function testSetAttributesAllowedString()
    {
        $this->_filter->setAttributesAllowed('class');
        $this->assertEquals(['class' => null], $this->_filter->getAttributesAllowed());
    }

    /**
     * Ensures that setAttributesAllowed() follows expected behavior when provided an array of attributes.
     */
    public function testSetAttributesAllowedArray()
    {
        $attributesAllowed = [
            'clAss',
            4 => 'inT',
            'ok' => 'String',
            null,
        ];
        $this->_filter->setAttributesAllowed($attributesAllowed);
        $attributesAllowedExpected = [
            'class' => null,
            'int' => null,
            'string' => null,
        ];
        $this->assertEquals($attributesAllowedExpected, $this->_filter->getAttributesAllowed());
    }

    /**
     * Ensures that a single unclosed tag is stripped in its entirety.
     */
    public function testFilterTagUnclosed1()
    {
        $input = '<a href="http://example.com" Some Text';
        $expected = '';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that a single tag is stripped.
     */
    public function testFilterTag1()
    {
        $input = '<a href="example.com">foo</a>';
        $expected = 'foo';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that singly nested tags are stripped.
     */
    public function testFilterTagNest1()
    {
        $input = '<a href="example.com"><b>foo</b></a>';
        $expected = 'foo';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that two successive tags are stripped.
     */
    public function testFilterTag2()
    {
        $input = '<a href="example.com">foo</a><b>bar</b>';
        $expected = 'foobar';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that an allowed tag is returned as lowercase and with backward-compatible XHTML ending, where supplied.
     */
    public function testFilterTagAllowedBackwardCompatible()
    {
        $input = '<BR><Br><bR><br/><br  /><br / ></br></bR>';
        $expected = '<br><br><br><br /><br /><br></br></br>';
        $this->_filter->setTagsAllowed('br');
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that any greater-than symbols '>' are removed from text preceding a tag.
     */
    public function testFilterTagPrefixGt()
    {
        $input = '2 > 1 === true<br/>';
        $expected = '2  1 === true';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that any greater-than symbols '>' are removed from text having no tags.
     */
    public function testFilterGt()
    {
        $input = '2 > 1 === true ==> $object->property';
        $expected = '2  1 === true == $object-property';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that any greater-than symbols '>' are removed from text wrapping a tag.
     */
    public function testFilterTagWrappedGt()
    {
        $input = '2 > 1 === true <==> $object->property';
        $expected = '2  1 === true  $object-property';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that an attribute for an allowed tag is stripped.
     */
    public function testFilterTagAllowedAttribute()
    {
        $tagsAllowed = 'img';
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input = '<IMG alt="foo" />';
        $expected = '<img />';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that an allowed tag with an allowed attribute is filtered as expected.
     */
    public function testFilterTagAllowedAttributeAllowed()
    {
        $tagsAllowed = [
            'img' => 'alt',
        ];
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input = '<IMG ALT="FOO" />';
        $expected = '<img alt="FOO" />';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures expected behavior when a greater-than symbol '>' appears in an allowed attribute's value.
     *
     * Currently this is not unsupported; these symbols should be escaped when used in an attribute value.
     */
    public function testFilterTagAllowedAttributeAllowedGt()
    {
        $tagsAllowed = [
            'img' => 'alt',
        ];
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input = '<img alt="$object->property" />';
        $expected = '<img>property" /';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures expected behavior when an escaped greater-than symbol '>' appears in an allowed attribute's value.
     */
    public function testFilterTagAllowedAttributeAllowedGtEscaped()
    {
        $tagsAllowed = [
            'img' => 'alt',
        ];
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input = '<img alt="$object-&gt;property" />';
        $expected = '<img alt="$object-&gt;property" />';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that an unterminated attribute value does not affect other attributes but causes the corresponding
     * attribute to be removed in its entirety.
     */
    public function testFilterTagAllowedAttributeAllowedValueUnclosed()
    {
        $tagsAllowed = [
            'img' => ['alt', 'height', 'src', 'width'],
        ];
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input = '<img src="image.png" alt="square height="100" width="100" />';
        $expected = '<img src="image.png" alt="square height=" width="100" />';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that an allowed attribute having no value is removed (XHTML disallows attributes with no values).
     */
    public function testFilterTagAllowedAttributeAllowedValueMissing()
    {
        $tagsAllowed = [
            'input' => ['checked', 'name', 'type'],
        ];
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input = '<input name="foo" type="checkbox" checked />';
        $expected = '<input name="foo" type="checkbox" />';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that the filter works properly for the data reported on fw-general on 2007-05-26.
     *
     * @see    http://www.nabble.com/question-about-tag-filter-p10813688s16154.html
     */
    public function testFilter20070526()
    {
        $tagsAllowed = [
            'object' => ['width', 'height'],
            'param' => ['name', 'value'],
            'embed' => ['src', 'type', 'wmode', 'width', 'height'],
        ];
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input = '<object width="425" height="350"><param name="movie" value="http://www.example.com/path/to/movie">'
               . '</param><param name="wmode" value="transparent"></param><embed '
               . 'src="http://www.example.com/path/to/movie" type="application/x-shockwave-flash" '
               . 'wmode="transparent" width="425" height="350"></embed></object>';
        $expected = '<object width="425" height="350"><param name="movie" value="http://www.example.com/path/to/movie">'
               . '</param><param name="wmode" value="transparent"></param><embed '
               . 'src="http://www.example.com/path/to/movie" type="application/x-shockwave-flash" '
               . 'wmode="transparent" width="425" height="350"></embed></object>';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that a comment is stripped.
     */
    public function testFilterComment()
    {
        $input = '<!-- a comment -->';
        $expected = '';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that a comment wrapped with other strings is stripped.
     */
    public function testFilterCommentWrapped()
    {
        $input = 'foo<!-- a comment -->bar';
        $expected = 'foobar';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that a comment IS removed when comments are flagged as allowed.
     *
     * @group ZF-8473
     */
    public function testSpecifyingCommentsAllowedStillStripsComments()
    {
        $input = '<!-- a comment -->';
        $expected = '';
        $this->_filter->setCommentsAllowed(true);
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that a comment containing tags is untouched when comments are allowed.
     *
     * @group ZF-8473
     */
    public function testSpecifyingCommentsAllowedStripsCommentsContainingTags()
    {
        $input = '<!-- a comment <br /> <h1>SuperLarge</h1> -->';
        $expected = '';
        $this->_filter->setCommentsAllowed(true);
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures expected behavior when comments are marked as allowed (in our
     * case, this should have no effect) and a comment contains tags and
     * linebreaks.
     *
     * @group ZF-8473
     */
    public function testSpecifyingCommentsAllowedFiltersCommentsContainingTagsAndLinebreaks()
    {
        $input = "<br> test <p> text </p> with <!-- comments --> and <!-- hidd\n\nen <br> -->";
        $expected = ' test  text  with  and ';
        $this->_filter->setCommentsAllowed(true);
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures expected behavior when comments are allowed but nested.
     *
     * @group ZF-8473
     */
    public function testSpecifyingCommentsAllowedShouldStillStripNestedComments()
    {
        $input = '<a> <!-- <b> <!-- <c> --> <d> --> <e>';
        $expected = '  ';
        $this->_filter->setCommentsAllowed(true);
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that space between double-hyphen and closing bracket still matches as a comment delimiter.
     *
     * @group ZF-8473
     *
     * @see    http://www.w3.org/TR/1999/REC-html401-19991224/intro/sgmltut.html#h-3.2.4
     */
    public function testFilterCommentsAllowedDelimiterEndingWhiteSpace()
    {
        $input = '<a> <!-- <b> --  > <c>';
        $expected = '  ';
        $this->_filter->setCommentsAllowed(true);
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that a closing angle bracket in an allowed attribute does not break the parser.
     *
     * @see   http://framework.zend.com/issues/browse/ZF-3278
     */
    public function testClosingAngleBracketInAllowedAttributeValue()
    {
        $tagsAllowed = [
            'a' => 'href',
        ];
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input = '<a href="Some &gt; Text">';
        $expected = '<a href="Some &gt; Text">';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that an allowed attribute's value may end with an equals sign '='.
     *
     * @group ZF-3293
     * @group ZF-5983
     */
    public function testAllowedAttributeValueMayEndWithEquals()
    {
        $tagsAllowed = [
            'element' => 'attribute',
        ];
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input = '<element attribute="a=">contents</element>';
        $this->assertEquals($input, $this->_filter->filter($input));
    }

    /**
     * @group ZF-5983
     */
    public function testDisallowedAttributesSplitOverMultipleLinesShouldBeStripped()
    {
        $tagsAllowed = ['a' => 'href'];
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input = '<a href="http://framework.zend.com/issues" onclick
=
    "alert(&quot;Gotcha&quot;); return false;">http://framework.zend.com/issues</a>';
        $filtered = $this->_filter->filter($input);
        $this->assertStringNotContainsString('onclick', $filtered);
    }

    /**
     * @ZF-8828
     */
    public function testFilterIsoChars()
    {
        $input = 'äöü<!-- a comment -->äöü';
        $expected = 'äöüäöü';
        $this->assertEquals($expected, $this->_filter->filter($input));

        $input = 'äöü<!-- a comment -->äöü';
        $input = iconv('UTF-8', 'ISO-8859-1', $input);
        $output = $this->_filter->filter($input);
        $this->assertFalse(empty($output));
    }

    /**
     * @ZF-8828
     */
    public function testFilterIsoCharsInComment()
    {
        $input = 'äöü<!--üßüßüß-->äöü';
        $expected = 'äöüäöü';
        $this->assertEquals($expected, $this->_filter->filter($input));

        $input = 'äöü<!-- a comment -->äöü';
        $input = iconv('UTF-8', 'ISO-8859-1', $input);
        $output = $this->_filter->filter($input);
        $this->assertFalse(empty($output));
    }

    /**
     * @ZF-8828
     */
    public function testFilterSplitCommentTags()
    {
        $input = 'äöü<!-->üßüßüß<-->äöü';
        $expected = 'äöüäöü';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * @group ZF-9434
     */
    public function testCommentWithTagInSameLine()
    {
        $input = 'test <!-- testcomment --> test <div>div-content</div>';
        $expected = 'test  test div-content';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * @group ZF-9833
     */
    public function testMultiParamArray()
    {
        $filter = new Zend_Filter_StripTags(['a','b','hr'],[],true);

        $input = 'test <a /> test <div>div-content</div>';
        $expected = 'test <a /> test div-content';
        $this->assertEquals($expected, $filter->filter($input));
    }

    /**
     * @group ZF-9828
     */
    public function testMultiQuoteInput()
    {
        $filter = new Zend_Filter_StripTags(
            [
                'allowTags' => 'img',
                'allowAttribs' => ['width', 'height', 'src'],
            ]
        );

        $input = '<img width="10" height="10" src=\'wont_be_matched.jpg\'>';
        $expected = '<img width="10" height="10" src=\'wont_be_matched.jpg\'>';
        $this->assertEquals($expected, $filter->filter($input));
    }

    /**
     * @group ZF-10256
     */
    public function testNotClosedHtmlCommentAtEndOfString()
    {
        $input = 'text<!-- not closed comment at the end';
        $expected = 'text';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * @group ZF-11617
     */
    public function testFilterCanAllowHyphenatedAttributeNames()
    {
        $input = '<li data-disallowed="no!" data-name="Test User" data-id="11223"></li>';
        $expected = '<li data-name="Test User" data-id="11223"></li>';

        $this->_filter->setTagsAllowed('li');
        $this->_filter->setAttributesAllowed(['data-id','data-name']);

        $this->assertEquals($expected, $this->_filter->filter($input));
    }
}

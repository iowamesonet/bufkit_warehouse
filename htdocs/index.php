<?php
require_once "../config/settings.php";
date_default_timezone_set("UTC");

$a_now = date("Y-m-d H:00:00");
$at = strtotime($a_now);
$a_year = date("Y", $at);
$a_month = date("m", $at);
$a_day = date("d", $at);

$archive = MTARCHIVE . $a_year . "/" . $a_month . "/" . $a_day . "/bufkit/";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 
      Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">


<html>

<head>
    <title>The Bufkit Warehouse</title>
    <link rel="stylesheet" href="/css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" type="text/css" href="settings.css">
    <meta name="keywords" content="bufkit, bufkit warehouse, bufkit data, 
bufkit documentation, bufkit files, bufkit download, bufkit links, install 
bufkit, bufget, use bufkit, bufkit archive">
</head>

<body text="#FFFFFF">

    <div id="wrapper">

        <div id="header">
            <center>
                <img
                    src="bufkit.png">
            </center>
        </div>

        <div id="menu">

            <table style="table-layout:fixed" align="center">
                <tr valign="middle">

                    <td class="menusplit"> </td>
                    <td class="menu">Home</td>
                    <td class="menusplit"> </td>
                    <td class="menu"><a href="downloads.html">Downloads</a></td>
                    <td class="menusplit"> </td>
                    <td class="menu"><a href="files.html">Helpful Files</a></td>
                    <td class="menusplit"> </td>
                    <td class="menu"><a href="data/index.html">Data Sources</a></td>

                    <td class="menusplit"> </td>
                    <td class="menu"><a href="documentation.html">Documentation</a></td>
                    <td class="menusplit"> </td>
                    <td class="menu"><a href="publications.html">Publications</a></td>
                    <td class="menusplit"> </td>
                    <td class="menu"><a href="links.html">Additional Links</a></td>

                    <td class="menusplit"> </td>
                    <td class="menu" , color="333333"><a href="acknowledgements.html">Acknowledgements</a></td>
                    <td class="menusplit"> </td>
                </tr>
            </table>

        </div>

        <div id="contentblock">
            <div id="content2">

                <table>
                    <tr>
                        <td valign="top" align="left">

                            <h2>
                                <u>Information Links</u>:
                            </h2>

                            <p>
                            <h2>
                                <ul>
                                    <a href="whatsbufkit.html">What Is Bufkit?</a>
                                </ul>
                            </h2>
                            </p>
                            <p>
                            <h2>
                                <ul>
                                    <a href="instructions.html">How Do I Install and Run Bufkit?</a>
                            </h2>
                            </ul>
                            </p>

                            <p>
                            <h2>
                                <ul>
                                    <a href="https://www.erh.noaa.gov/er/buf/bufkit/bufkitdocs.html">How Do I
                                        Use Bufkit?</a>
                                </ul>
                            </h2>
                            </p>

                            <p>
                            <h2>
                                <ul>
                                    <a href="https://www.wdtb.noaa.gov/tools/bufkit/bufget.html">How Do I Use
                                        BufGet?</a>
                                </ul>
                            </h2>
                            </p>

                            <h2>
                                <u>Site Links</u>:
                            </h2>

                            <p>
                            <h2>
                                <ul>
                                    <a href="/data">Data Sources</a>
                                </ul>
                            </h2>
                            </p>

                            <p>
                            <h2>
                                <ul>
                                    <a href="/documentation.html">Documentation</a>
                            </h2>
                            </ul>
                            </p>

                            <p>
                            <h2>
                                <ul>
                                    <a href="/publications.html">Publications</a>
                            </h2>
                            </ul>
                            </p>

                            <p>
                            <h2>
                                <ul>
                                    <a href="/downloads.html">Downloads</a>
                            </h2>
                            </ul>
                            </p>

                            <p>
                            <h2>
                                <ul>
                                    <a href="/files.html">Helpful Files</a>
                            </h2>
                            </ul>
                            </p>

                            <p>
                            <h2>
                                <ul>
                                    <a href="/links.html">Additional Links</a>
                            </h2>
                            </ul>
                            </p>

                            <p>
                            <h2>
                                <ul>
                                    <a href="/acknowledgements.html">Acknowledgements</a>
                            </h2>
                            </ul>
                            </p>

                        </td>
                        <td>


                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td align="justify">


                            <h3>
                                <center>
                                    <u>Website Announcements</u>:
                                </center>
                            </h3>
                            <p>
                                <strong>11 Sep 2026</strong>: This website has a new home at https://bufkitwarehouse.org and
                                work continues to make improvements to the system and support new models, like RRFS.
                            </p>

                            <p>
                            <h3>
                                <ul>
                                    <u>Announcement</u>: Dec. 30, 2010 - Thanks to a recent upgrade in computer infrastructure at ISU, the Bufkit Warehouse is now generating RUC bufkit profiles for the Contiguous USA.
                                    Additionally, these files are post-processed using the Cobb method (version 5.4) for each site. Links have been updated on the <a href="data/index.html">google map</a> page.
                                    Also, the <a href="image_loader.phtml">meteogram charts</a> have been updated to include the latest RUC data available. The RUC data becomes available approximately 1.5 hours after
                                    initialization, with exception for the 00z and 12z runs, which come in about 2 hours after (longer DA time). Lastly, I am now archiving all of the bufkit data I
                                    generate. A link to the current day's archive may be found <a href="<?php echo $archive; ?>">here</a>. Big thanks to Daryl Herzmann and the <a
                                        href="https://mesonet.agron.iastate.edu">IEM</a> for help
                                    in setting this up. Don't have
                                    too much fun...
                                    </p>
                                    <p>
                                        <u>Announcement</u>: Jan. 23, 2009 - The Bufkit Warehouse has begun providing GFS and NAM bufkit profiles for the Contiguous USA. Additionally, the profiles are sent to
                                        a perl script and the Cobb output is generated for each site. You may find
                                        links to all of the above on a google map <a href="data/index.html">here</a>. Lastly, you can view meteograms of the model output and Cobb snowfall projections, along
                                        with MOS forecasts, NWS point forecasts, and the nearest ASOS <a href="image_loader.phtml">here</a>. Please note: All of these tools were designed for Mozilla
                                        Firefox... I have no guarentees they will work in IE, Chrome, etc... with time, I hope to work out these compatibility issues. Enjoy!
                                    </p>
                                    <p>
                                        <u>Profile availability:</u> Scripts to generate the bufkit profiles for the NAM are initiated (model run time + 3 hrs), and for the GFS (model run time + 5 hrs). It
                                        takes about 30 min. for all the sites + cobb output to be generated. If you would like some sites to be bumped up near the top of the list (to get generated earlier),
                                        let me know...
                            </h3>
                            </ul>
                            </p>

                    </tr>
                    </td>
                </table>

                <p>
                <h5>
                    <center>
                        If there is anything you would like to see changed or added to this page,
                        please contact me at: ckarsten@iastate.edu
                        </p>
                        <p>

                            Updated 24 July 2011
                    </center>
                </h5>
                </p>

                <br>
            </div>

</body>

</html>
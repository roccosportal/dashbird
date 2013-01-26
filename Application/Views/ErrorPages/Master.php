<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>error page</title>
        <style>
            * {
                font-size: 10px;
                margin: 0px;
                padding: 0px;

}
body {
    background-color: #B6D5DE;
}
#contentholder {
    width : 600px;
    margin : auto;
}
            #error {
                color : red;
                font-weight: bold;
                padding: 20px 10px;
                margin-bottom: 5px;
                background-color: #7BBDD1;
                font-size: 13px
            }
            #message, #stacktrace, #logtrace, #code, #file {
                 padding: 5px 10px 20px 20px;
            }
            span {
                font-size: 11px;
                font-weight: bold;
}
        </style>
    </head>
    <body>
        <div id="contentholder">
            <div id="error">Unfortunaly there was an unhandled expception and the site caused an error.<br />See below for error details.</div>
            <span>Message:</span>
            <div id="message"><?php echo nl2br($Exception->getMessage()) ?></div>
            <span>File:</span>
            <div id="file">File  <?php echo  $Exception->getFile() ?> at line <?php echo  $Exception->getLine() ?></div>
            <span>Stacktrace:</span>
            <div id="stacktrace"><?php echo  nl2br($Exception->getTraceAsString()) ?></div>
            <span>Logtrace:</span>
            <div id="logtrace"><?php echo  nl2br(\Pvik\Core\Log::GetTrace()) ?></div>
            <span>Code:</span>
            <div id="code"><?php echo  nl2br($Exception->getCode()) ?></div>
        </div>
    </body>
</html>

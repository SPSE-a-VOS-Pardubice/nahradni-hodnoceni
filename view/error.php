<?php

declare(strict_types=1);

use Spse\NahradniHodnoceni\Model\DatabaseEntity;
use Spse\NahradniHodnoceni\Model\ViewableProperty;
use Spse\NahradniHodnoceni\Model\ViewablePropertyType;

?>

<hmtl>
<head>
<title>Error</title>
</head>
<body>
<p><?php echo ($args["data"]["text"]); ?></p>
<a href=" <?php echo ($args["data"]["link"]); ?> ">Go back</a>
</body>
</hmtl>
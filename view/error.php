<?php

declare(strict_types=1);

use Spse\NahradniHodnoceni\Model\DatabaseEntity;
use Spse\NahradniHodnoceni\Model\DatabaseEntityProperty;
use Spse\NahradniHodnoceni\Model\DatabaseEntityPropertyType;

?>

<hmtl>
<head>
<title>Error</title>
</head>
<body>
<p><?php echo ($args["data"]["message"]); ?></p>
<a href=" <?php echo ($args["data"]["backLink"]); ?> ">Go back</a>
</body>
</hmtl>
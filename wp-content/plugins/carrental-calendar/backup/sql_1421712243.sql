-- Car Rental Wordpress Plugin
-- Date: 2015-01-20 00:04:03

INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_available_languages', 'a:2:{s:5:\"cs_CZ\";a:11:{s:7:\"country\";s:14:\"Czech Republic\";s:11:\"country-www\";s:2:\"cz\";s:4:\"lang\";s:5:\"Czech\";s:11:\"lang-native\";s:9:\"Čeština\";s:8:\"lang-www\";s:5:\"cs-CZ\";s:3:\"dir\";s:3:\"ltr\";s:6:\"ms-loc\";s:3:\"csy\";s:5:\"ms-cp\";s:6:\"CP1250\";s:10:\"google-api\";s:2:\"cs\";s:13:\"microsoft-api\";s:2:\"cs\";s:6:\"active\";b:1;}s:5:\"sk_SK\";a:11:{s:7:\"country\";s:8:\"Slovakia\";s:11:\"country-www\";s:2:\"sk\";s:4:\"lang\";s:6:\"Slovak\";s:11:\"lang-native\";s:32:\"Slovenčina/Slovenská republika\";s:8:\"lang-www\";s:5:\"sk-SK\";s:3:\"dir\";s:3:\"ltr\";s:6:\"ms-loc\";s:3:\"sky\";s:5:\"ms-cp\";s:6:\"CP1250\";s:10:\"google-api\";s:2:\"sk\";s:13:\"microsoft-api\";s:2:\"sk\";s:6:\"active\";b:0;}}')
										 ON DUPLICATE KEY UPDATE `option_value` = 'a:2:{s:5:\"cs_CZ\";a:11:{s:7:\"country\";s:14:\"Czech Republic\";s:11:\"country-www\";s:2:\"cz\";s:4:\"lang\";s:5:\"Czech\";s:11:\"lang-native\";s:9:\"Čeština\";s:8:\"lang-www\";s:5:\"cs-CZ\";s:3:\"dir\";s:3:\"ltr\";s:6:\"ms-loc\";s:3:\"csy\";s:5:\"ms-cp\";s:6:\"CP1250\";s:10:\"google-api\";s:2:\"cs\";s:13:\"microsoft-api\";s:2:\"cs\";s:6:\"active\";b:1;}s:5:\"sk_SK\";a:11:{s:7:\"country\";s:8:\"Slovakia\";s:11:\"country-www\";s:2:\"sk\";s:4:\"lang\";s:6:\"Slovak\";s:11:\"lang-native\";s:32:\"Slovenčina/Slovenská republika\";s:8:\"lang-www\";s:5:\"sk-SK\";s:3:\"dir\";s:3:\"ltr\";s:6:\"ms-loc\";s:3:\"sky\";s:5:\"ms-cp\";s:6:\"CP1250\";s:10:\"google-api\";s:2:\"sk\";s:13:\"microsoft-api\";s:2:\"sk\";s:6:\"active\";b:0;}}';
INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_primary_language', 'cs_CZ')
										 ON DUPLICATE KEY UPDATE `option_value` = 'cs_CZ';
INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_global_currency', 'CZK')
										 ON DUPLICATE KEY UPDATE `option_value` = 'CZK';
INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_consumption', 'eu')
										 ON DUPLICATE KEY UPDATE `option_value` = 'eu';
INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_available_currencies', 'a:3:{s:3:\"HRK\";s:5:\"3.000\";s:3:\"EUR\";s:6:\"25.000\";s:3:\"USD\";s:6:\"20.000\";}')
										 ON DUPLICATE KEY UPDATE `option_value` = 'a:3:{s:3:\"HRK\";s:5:\"3.000\";s:3:\"EUR\";s:6:\"25.000\";s:3:\"USD\";s:6:\"20.000\";}';
INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_overbooking', 'no')
										 ON DUPLICATE KEY UPDATE `option_value` = 'no';
INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_any_location_search', 'yes')
										 ON DUPLICATE KEY UPDATE `option_value` = 'yes';
INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_paypal', 'pavel@ecalypse.com')
										 ON DUPLICATE KEY UPDATE `option_value` = 'pavel@ecalypse.com';
INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_require_payment', 'yes')
										 ON DUPLICATE KEY UPDATE `option_value` = 'yes';
INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_distance_metric', 'km')
										 ON DUPLICATE KEY UPDATE `option_value` = 'km';
INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_company_info', 'a:11:{s:4:\"name\";s:18:\"Petrova půjčovna\";s:2:\"id\";s:5:\"31354\";s:3:\"vat\";s:4:\"3131\";s:5:\"email\";s:18:\"pujcovna@upetra.cz\";s:5:\"phone\";s:14:\"+4207325133434\";s:3:\"fax\";s:0:\"\";s:6:\"street\";s:17:\"Dolní Bečva 330\";s:4:\"city\";s:13:\"Dolní Bečva\";s:3:\"zip\";s:5:\"75655\";s:7:\"country\";s:2:\"CZ\";s:3:\"web\";s:20:\"http://www.upetra.cz\";}')
										 ON DUPLICATE KEY UPDATE `option_value` = 'a:11:{s:4:\"name\";s:18:\"Petrova půjčovna\";s:2:\"id\";s:5:\"31354\";s:3:\"vat\";s:4:\"3131\";s:5:\"email\";s:18:\"pujcovna@upetra.cz\";s:5:\"phone\";s:14:\"+4207325133434\";s:3:\"fax\";s:0:\"\";s:6:\"street\";s:17:\"Dolní Bečva 330\";s:4:\"city\";s:13:\"Dolní Bečva\";s:3:\"zip\";s:5:\"75655\";s:7:\"country\";s:2:\"CZ\";s:3:\"web\";s:20:\"http://www.upetra.cz\";}';
INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_reservation_email_en_GB', 'Dear [CustomerName],a

thank you for your reservation. Here are your reservation details:
[ReservationDetails]
[ReservationNumber]

You can return to your reservation summary page anytime by going to this link:
[ReservationLink]

We are also sending this information to the email address you have provided.

If you would like to change the reservation details, you can do so by calling our office at:
+123 456 789 or by email example@example.org

[ReservationLinkStart]Click here[ReservationLinkEnd] to print your reservation - takes them to reservation summary print out.

Thank you for your business!
									    ')
										 ON DUPLICATE KEY UPDATE `option_value` = 'Dear [CustomerName],a

thank you for your reservation. Here are your reservation details:
[ReservationDetails]
[ReservationNumber]

You can return to your reservation summary page anytime by going to this link:
[ReservationLink]

We are also sending this information to the email address you have provided.

If you would like to change the reservation details, you can do so by calling our office at:
+123 456 789 or by email example@example.org

[ReservationLinkStart]Click here[ReservationLinkEnd] to print your reservation - takes them to reservation summary print out.

Thank you for your business!
									    ';
INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_book_send_email', 'a:2:{s:6:\"client\";i:1;s:5:\"admin\";i:1;}')
										 ON DUPLICATE KEY UPDATE `option_value` = 'a:2:{s:6:\"client\";i:1;s:5:\"admin\";i:1;}';
INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_reservation_email_cs_CZ', 'Dear [CustomerName],asdasd

thank you for your reservation. Here are your reservation details:
[ReservationDetails]
[ReservationNumber]

You can return to your reservation summary page anytime by going to this link:
[ReservationLink]

We are also sending this information to the email address you have provided.

If you would like to change the reservation details, you can do so by calling our office at:
+123 456 789 or by email example@example.org

[ReservationLinkStart]Click here[ReservationLinkEnd] to print your reservation - takes them to reservation summary print out.

Thank you for your business!
									    									    ')
											 ON DUPLICATE KEY UPDATE `option_value` = 'Dear [CustomerName],asdasd

thank you for your reservation. Here are your reservation details:
[ReservationDetails]
[ReservationNumber]

You can return to your reservation summary page anytime by going to this link:
[ReservationLink]

We are also sending this information to the email address you have provided.

If you would like to change the reservation details, you can do so by calling our office at:
+123 456 789 or by email example@example.org

[ReservationLinkStart]Click here[ReservationLinkEnd] to print your reservation - takes them to reservation summary print out.

Thank you for your business!
									    									    ';
INSERT INTO `wp_options` (`option_name`, `option_value`) VALUES ('carrental_reservation_email_sk_SK', 'Dear [CustomerName],

thank you for your reservation. Here are your reservation details:
[ReservationDetails]
[ReservationNumber]

You can return to your reservation summary page anytime by going to this link:
[ReservationLink]

We are also sending this information to the email address you have provided.

If you would like to change the reservation details, you can do so by calling our office at:
+123 456 789 or by email example@example.org

[ReservationLinkStart]Click here[ReservationLinkEnd] to print your reservation - takes them to reservation summary print out.

Thank you for your business!')
											 ON DUPLICATE KEY UPDATE `option_value` = 'Dear [CustomerName],

thank you for your reservation. Here are your reservation details:
[ReservationDetails]
[ReservationNumber]

You can return to your reservation summary page anytime by going to this link:
[ReservationLink]

We are also sending this information to the email address you have provided.

If you would like to change the reservation details, you can do so by calling our office at:
+123 456 789 or by email example@example.org

[ReservationLinkStart]Click here[ReservationLinkEnd] to print your reservation - takes them to reservation summary print out.

Thank you for your business!';

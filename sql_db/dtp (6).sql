-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2026 at 07:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dtp`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(2, 'DTPadmin', '$2y$10$DSCf/FivMdpwyr2nNHUHGeFzkQxyDb84Vqj4GjTCDCNBqF1xqAP/y');

-- --------------------------------------------------------

--
-- Table structure for table `admin_fcm_tokens`
--

CREATE TABLE `admin_fcm_tokens` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `token` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_fcm_tokens`
--

INSERT INTO `admin_fcm_tokens` (`id`, `admin_id`, `token`, `created_at`, `updated_at`) VALUES
(66, 2, 'f9l8kgwCD760IboolojRtJ:APA91bFRamrkQBG2omBrF7kbHN9yv9_oe6DHQmVRFny4HS62Is8-1hPX-cCCAQ-2hHeI_Y21IFAspF8MebBgJi4ML8FQFHCpZdKPONz-ZiFC9OtF1qj8tHY', '2026-07-01 08:06:09', '2026-07-01 08:06:09');

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `id` int(11) NOT NULL,
  `bus_name` varchar(150) DEFAULT NULL,
  `bus_number` varchar(50) DEFAULT NULL,
  `from_location` varchar(150) DEFAULT NULL,
  `to_location` varchar(150) DEFAULT NULL,
  `travel_date` date DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total_seats` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`id`, `bus_name`, `bus_number`, `from_location`, `to_location`, `travel_date`, `departure_time`, `arrival_time`, `price`, `total_seats`, `description`, `banner_image`, `status`, `created_at`) VALUES
(3, 'Chitwan Yatayat', '7777777', 'Chitwan', 'India', '2026-05-08', '00:00:00', '00:00:00', 5001.00, 50, 'hekloooooooooooooooooo', '1778060024_TTMS (1).jpg', 1, '2026-05-06 09:33:44');

-- --------------------------------------------------------

--
-- Table structure for table `bus_inquiries`
--

CREATE TABLE `bus_inquiries` (
  `id` int(11) NOT NULL,
  `bus_id` int(11) DEFAULT NULL,
  `travel_date` date DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_quiries`
--

CREATE TABLE `chatbot_quiries` (
  `id` int(11) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `question_pattern` text DEFAULT NULL,
  `answer` text NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chatbot_quiries`
--

INSERT INTO `chatbot_quiries` (`id`, `keyword`, `question_pattern`, `answer`, `category`, `status`, `created_at`) VALUES
(1, 'hi,hello,hey,namaste', 'greeting questions', 'Hello! Welcome to Digital Tourism Platform. How can I help you plan your trip?', 'greeting', 1, '2026-06-20 10:57:54'),
(2, 'tour,package,trip,holiday,vacation', 'asking about tour packages', 'We provide domestic and international tour packages. You can explore our available packages from the Tours section.', 'tour', 1, '2026-06-20 10:57:54'),
(3, 'price,cost,fee,amount,charge', 'asking package price', 'Package prices depend on destination, duration and included services. Please check the package details for exact pricing.', 'pricing', 1, '2026-06-20 10:57:54'),
(4, 'book,booking,reserve,reservation', 'asking how to book', 'You can book a package by opening the tour details page and clicking the Book Now button.', 'booking', 1, '2026-06-20 10:57:54'),
(5, 'bus,bus ticket,transport', 'asking about bus service', 'We provide bus ticket booking services for different destinations.', 'bus', 1, '2026-06-20 10:57:54'),
(6, 'flight,airplane,air ticket,airfare', 'asking about flights', 'We provide flight booking assistance for domestic and international travel.', 'flight', 1, '2026-06-20 10:57:54'),
(7, 'visa,passport,document', 'asking visa service', 'We provide visa consultation and documentation support.', 'visa', 1, '2026-06-20 10:57:54'),
(8, 'trek,trekking,hiking,everest,annapurna', 'asking trekking packages', 'We provide trekking packages including Everest, Annapurna and other adventure destinations.', 'trekking', 1, '2026-06-20 10:57:54'),
(9, 'hotel,stay,accommodation', 'asking hotel information', 'We can help you plan your trip with accommodation options.', 'hotel', 1, '2026-06-20 10:57:54'),
(10, 'contact,phone,email,address', 'asking contact details', 'You can contact Digital Tourism Platform through our Contact Us page.', 'support', 1, '2026-06-20 10:57:54'),
(11, 'thank,thanks', 'expressing thanks', 'You are welcome! Feel free to ask if you need help planning your journey.', 'greeting', 1, '2026-06-20 10:57:54'),
(12, 'bye,goodbye', 'ending conversation', 'Thank you for visiting Digital Tourism Platform. Have a great journey!', 'greeting', 1, '2026-06-20 10:57:54');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `logo`, `status`, `created_at`) VALUES
(2, 'Mero Kinmel', '1768153624_client.png', 1, '2026-01-11 12:31:10'),
(3, 'Boston College', '1768153348_boston.png', 1, '2026-01-11 17:42:28'),
(5, 'CMT Hotel', '1768153425_cmt.png', 1, '2026-01-11 17:43:45'),
(6, 'Doko Namlo', '1768153516_client.jpeg', 1, '2026-01-11 17:44:18'),
(7, 'V Group', '1768153789_client.png', 1, '2026-01-11 17:48:54'),
(9, 'A Star Consultancy  ', '1768154288_client.jpg', 1, '2026-01-11 17:57:26'),
(10, 'Presidency College', '1768154425_client.png', 1, '2026-01-11 17:59:54'),
(11, 'Jalap Nepal', '1768154769_jalap.jpg', 1, '2026-01-11 18:06:09'),
(12, 'Dreams College', '1768155043_dreams_college.png', 1, '2026-01-11 18:10:43');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flights`
--

CREATE TABLE `flights` (
  `id` int(11) NOT NULL,
  `from_city` varchar(100) NOT NULL,
  `to_city` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` varchar(50) DEFAULT NULL,
  `is_group_fare` tinyint(1) DEFAULT 0,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_albums`
--

CREATE TABLE `gallery_albums` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_albums`
--

INSERT INTO `gallery_albums` (`id`, `title`, `slug`, `cover_image`, `description`, `status`, `created_at`) VALUES
(4, 'Kathmandu Tour hello', 'kathmandu-tour-hello', 'istockphoto-530450181-612x612.jpg', NULL, 1, '2025-12-25 09:22:10');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_photos`
--

CREATE TABLE `gallery_photos` (
  `id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `trip_id`, `name`, `email`, `phone`, `message`, `created_at`) VALUES
(102, 5, 'Kushal Comp', 'comp.kushal@gmail.com', '9745355605', 'mnkjbfnfjkdfjkdhfjkd', '2026-03-23 09:47:24'),
(109, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'sdddddddddddd', '2026-07-01 05:25:57'),
(110, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'sdddddddddddd', '2026-07-01 05:28:05'),
(111, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'sdddddddddddd', '2026-07-01 05:28:14'),
(112, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'vvvvvvvvvvvvvv', '2026-07-01 05:30:24'),
(113, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'vvvvvvvvvvvvvv', '2026-07-01 05:31:27'),
(114, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'vvvvvvvvvvvvvv', '2026-07-01 05:32:12'),
(115, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'vvvvvvvvvvvvvv', '2026-07-01 05:33:25'),
(116, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'mmmmmmmmmm', '2026-07-01 05:39:01'),
(117, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'mmmmmmmmmm', '2026-07-01 06:11:23'),
(118, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'mmmmmmmmmm', '2026-07-01 06:18:35'),
(119, 5, 'Bipin Chapai', 'bipinchapai2059@gmail.com', '9745355644', 'vx dsfsdg dfgdfg', '2026-07-01 06:20:14');

-- --------------------------------------------------------

--
-- Table structure for table `package_bookings`
--

CREATE TABLE `package_bookings` (
  `id` int(11) NOT NULL,
  `package_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `travel_date` date DEFAULT NULL,
  `persons` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'confirmed',
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT '''pending''',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_bookings`
--

INSERT INTO `package_bookings` (`id`, `package_id`, `user_id`, `name`, `email`, `country`, `phone`, `travel_date`, `persons`, `message`, `status`, `payment_method`, `transaction_id`, `payment_status`, `created_at`) VALUES
(16, 5, 11, 'Bipin Chapai', 'bipinchapai2059@gmail.com', 'Nepal', '9745355605', '2026-06-10', 1, NULL, 'pending', 'eSewa', 'BOOK_1780920691', 'paid', '2026-06-08 12:12:10'),
(18, 5, 21, 'Kushal', 'acharyakushal629@gmail.com', 'Nepal', '9745355605', '2026-07-02', 1, NULL, 'confirmed', 'eSewa', 'BOOK_1782892999', 'paid', '2026-07-01 08:04:10');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recmnd_clicks`
--

CREATE TABLE `recmnd_clicks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `total_clicks` int(11) DEFAULT NULL,
  `clicked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recmnd_clicks`
--

INSERT INTO `recmnd_clicks` (`id`, `user_id`, `package_id`, `total_clicks`, `clicked_at`) VALUES
(1, 11, 10, 3, '2026-06-08 09:07:01'),
(7, 21, 5, 1, '2026-06-19 08:42:44');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `service` varchar(150) DEFAULT NULL,
  `review` text NOT NULL,
  `rating` tinyint(4) DEFAULT 5,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `service`, `review`, `rating`, `status`, `created_at`) VALUES
(3, 'Ganesh Poudel', 'Visa Service', 'Visa process was smooth and well guided.', 5, 1, '2026-01-11 11:38:06'),
(4, 'Ram Prasad', 'Camping', 'Got best camping package and equipment. Quick response and friendly support team.', 5, 1, '2026-01-11 11:39:46'),
(8, 'Raghav Pandey', 'Visa', 'smooth process', 5, 1, '2026-06-13 06:19:55');

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `price` varchar(50) DEFAULT NULL,
  `price_usd` decimal(10,2) DEFAULT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `overview` text DEFAULT NULL,
  `highlights` text DEFAULT NULL,
  `itinerary` text DEFAULT NULL,
  `includes` text DEFAULT NULL,
  `excludes` text DEFAULT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `is_popular` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` varchar(50) NOT NULL,
  `latitude` decimal(10,4) DEFAULT NULL,
  `longitude` decimal(10,4) DEFAULT NULL,
  `location_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `title`, `duration`, `price`, `price_usd`, `old_price`, `overview`, `highlights`, `itinerary`, `includes`, `excludes`, `pdf_file`, `banner_image`, `status`, `is_popular`, `created_at`, `type`, `latitude`, `longitude`, `location_name`) VALUES
(5, 'Everest Base Camp Trek', '14 Days', '50000', 550.00, 60000.00, 'Everest Base Camp Trek is one of the finest treks in the world that centers on the world\'s highest peak Mt. Everest (29,029 ft/ 8,848.68m). This trek will provide you with a natural thrill as it takes you through breathtaking high-altitude landscapes, esoteric Buddhist monasteries, traditional Sherpa villages, high-altitude flora and fauna, and snow-capped mountains.', 'The magnificent views of the world’s highest peak, Mt. Everest (8,848.68m)\r\nWorld’s highest airport at Syangboche (3,780m)\r\nExplore wide range of flora and fauna at Sagarmatha National Park\r\nWildlife like musk deer, colorful pheasants, snow leopards, and Himalayan Tahrs\r\nChance to explore the culture and lifestyles of the local Sherpa people\r\nPrayer wheels, colorful flags, Mani stones, high suspension bridges\r\nVisit an ancient monastery in Tengboche\r\nHighest glacier on Earth- Khumbu Glacier (4,900 m)\r\nAmazing panoramic views from Kala Patthar (5,555m)\r\nViews of other peaks such as Mt. Lhotse(8,516m), Cho Oyu (8,201m) and Mt. Makalu (8,463m)', 'Day\r\n1\r\nFlight from Kathmandu/Manthali to Lukla. Flight time: Approx 40 min from KTM/20 min from Manthali. Trek to Phakding (2,650 m). Trek time: Approx. 3 hrs.\r\nDay\r\n2\r\nTrek from Phakding to Namche Bazaar (3,440 m). Trek time: Approx. 6 hrs.\r\nDay\r\n3\r\nRest day and acclimatization at Namche Bazaar.\r\nDay\r\n4\r\nTrek from Namche to Tengboche/ Deboche (3,855 m). Trek time: Approx. 5 hrs.\r\nDay\r\n5\r\nTrek from Tengboche to Dingboche (4,360 m). Trek time: Approx. 5 hrs.\r\nDay\r\n6\r\nRest day and acclimatization at Dingboche.\r\nDay\r\n7\r\nTrek from Dingboche to Lobuche (4,930 m). Trek time: Approx. 5 hrs.\r\nDay\r\n8\r\nTrek from Lobuche to EBC (5,364 m) and back to Gorak Shep (5,185 m). Trek time: Approx. 7 hrs.\r\nDay\r\n9\r\nHike to Kala Patthar (5,555 m) viewpoint, trek to Gorak Shep, then to Pheriche (4,250 m). Trek time: Approx. 5 hrs.\r\nDay\r\n10\r\nTrek from Pheriche to Tengboche (3,855 m). Trek time: Approx. 5 hrs.\r\nDay\r\n11\r\nTrek from Tengboche to Namche Bazaar (3,440 m). Trek time: Approx. 5 hrs.\r\nDay\r\n12\r\nTrek from Namche Bazaar to Phakding (2,650 m). Trek time: Approx. 4 hrs.\r\nDay\r\n13\r\nTrek from Phakding to Lukla (2,850 m). Trek time: Approx. 4 hrs.\r\nDay\r\n14\r\nFly back to Kathmandu/ Manthali from Lukla. Flight time: Approx. 40 min for KTM/20 min for Manthali. Drive time: Approx. 5 hrs from Manthali to KTM', 'Transportation\r\nAccommodations\r\nFood\r\nGuide and Porter\r\nTrek permit and expenses\r\nMedical Assistance\r\nSouvenir\r\nFarewell', 'International Flight\r\nAccommodations\r\nFood\r\nGuide and Porter\r\nVisa\r\nTravel Insurance\r\nPersonal Expenses', '1766770952_Important-Questions.pdf', '1766770952_Everest-base-Camp-trek.jpeg', 1, 1, '2025-12-26 17:42:32', 'domestic', 28.0022, 86.8523, NULL),
(10, 'Manaslu Circuit Trek', '7 Days', '25000', 300.00, NULL, 'The Manaslu Circuit Trek is a rewarding Himalayan journey — a blend of rugged landscapes, cultural depth, and high-altitude adventure that leaves trekkers with memories of both breathtaking scenery and genuine human connection.', 'Scenic drive from Kathmandu to Soti Khola\r\nViews of the world\'s highest peaks- including Manaslu mountain (8,156m), Lamjung Himal, Mt.Annapurna II, etc.\r\nTrek along the Budhi Gandaki River gorge\r\nThe highest point on the trek - Larkya La Pass (5,106m / 16,751ft)\r\nRich biodiversity and beautiful natural scenery\r\nCaptivating flora and fauna\r\nInsight into Hindu and Buddhist culture\r\nPossibility of spotting wild endangered species like snow leopard', NULL, 'Transportation\r\nAccommodations\r\nFood\r\nGuide and Porter\r\nTrek Permits and Expenses\r\nMedical Assistance\r\nSouvenir\r\nFarewell', 'International Flight\r\nAccommodations\r\nFood\r\nGuide and Porter\r\nVisa\r\nTravel Insurance\r\nPersonal Expenses', '1768076727_Log-sheet-Sample.pdf', '1768076727_manaslu_circuit_trek.webp', 1, 0, '2026-01-10 20:25:27', 'domestic', NULL, NULL, NULL),
(13, 'Bali', '4 Nights / 5 Days', '115000', 800.00, NULL, 'This 4-night, 5-day Bali itinerary offers a blend of coastal adventure and cultural discovery. Based in the Kuta area, the trip includes water sports like banana boat rides at Tanjung Benoa and a sunset visit to the Uluwatu Temple. You will explore the island\'s interior with a visit to the Kintamani volcano and the artistic heritage of Ubud. A major highlight is a full-day fast boat excursion to Nusa Penida to see iconic landmarks such as Kelingking Beach and Angel\'s Billabong.', 'This 4-night Bali getaway combines Kuta\'s vibrant beaches and water sports with a scenic tour of the Kintamani volcano, the artistic charm of Ubud, and a stunning day trip to the iconic cliffs and shores of Nusa Penida.', NULL, 'International Ticket: KTM-DPS-KTM\r\n4 Nights hotel accommodation in Bali\r\nDaily breakfast at hotel\r\nAirport Transfer (Pick up and drop off)\r\nWatersports at Tanjung Benoa (Banana Boat)\r\nUluwatu Sunset Temple Tour\r\nKintamani Volcano viewpoint tour\r\nUbud Art Village exploration\r\nNusa Penida Island full-day tour with fast boat transfers\r\nLunch in Nusa Penida Island\r\nVisa fees\r\nAll tours and transfers on SIC Basis', 'Meals aside from those specifically included.\r\nCity and Resort Taxes (If Applicable)\r\nSurcharge (If Applicable)\r\nPersonal Expenses\r\nTips\r\nAny Other charge which is not mentioned in above inclusions', '1771841158_Important-Questions (1).pdf', '1771841158_bali-for-digital-nomads.jpg', 1, 1, '2026-02-23 10:05:58', 'international', NULL, NULL, NULL),
(15, 'Annapurna Base Camp Trek', '6 Days', '25500', 400.00, 30000.00, 'The Annapurna Base Camp (ABC) Trek is one of Nepal’s most renowned Himalayan adventures, set within the breathtaking Annapurna Sanctuary, a natural glacial basin enclosed by towering snow-covered peaks. The trek showcases an extraordinary variety of landscapes, from lush forests to high alpine terrain, combined with rich mountain culture and dramatic scenery. Surrounded by iconic summits including Annapurna I, Annapurna South, Hiunchuli, and the sacred Machhapuchhre, this trek offers a truly immersive and unforgettable Himalayan experience.', 'Close-up Himalayan views including Annapurna I & Machhapuchhre\r\nWalk inside the spectacular Annapurna Sanctuary\r\nSunrise and sunset at Annapurna Base Camp (4,130m)\r\nDramatic 360° mountain amphitheatre of snow peaks\r\nTraditional Gurung village experience in Chhomrong\r\nDense rhododendron, bamboo & alpine forests\r\nWaterfalls, rivers & suspension bridges along Modi Khola\r\nMachhapuchhre Base Camp panoramic viewpoint\r\nGlacier valley landscapes and high alpine terrain\r\nNatural hot spring experience at Jhinu\r\nDiverse landscapes from jungle to glacial basin\r\nPerfect mix of adventure + culture + nature', NULL, 'Pokhara – Jhinu Danda – Pokhara transportation (jeep)\r\nExperienced licensed trekking guide\r\nTeahouse/lodge accommodation during trek\r\n3 meals per day during trekking (Breakfast, Lunch, Dinner)', 'Personal trekking gear (jacket, sleeping bag, gloves, poles, etc.)\r\nSnacks, soft drinks, chocolates & bottled water\r\nPersonal expenses (extra food, drinks, shopping)\r\nAnything not mentioned in “Package Includes”', '1782919225_testing_pdf.pdf', '1782919225_day4abc.jpg', 1, 1, '2026-07-01 15:20:25', 'domestic', 28.5300, 83.8780, 'Annapurna Base Camp'),
(16, 'Langtang Trek', '6 Days', '20500', 400.00, 25000.00, 'Langtang Valley, located north of Kathmandu inside Langtang National Park, is one of Nepal’s most beautiful Himalayan trekking regions. This trail offers a perfect mix of snow peaks, glaciers, rivers, forests, culture, and mountain villages. The trek leads to Kyanjin Gompa, a high Himalayan settlement surrounded by dramatic peaks, and the famous viewpoint Kyanjin Ri, offering 360° Himalayan panoramas. This trek is perfect for travelers seeking real mountains, cultural experience, glacier views, and peaceful nature, all in just 6 days.', 'Close-up Himalayan views including Langtang Lirung\r\nBeautiful forests, rivers, waterfalls & suspension bridges\r\nVisit traditional Tamang & Tibetan-influenced villages\r\nExplore Kyanjin Gompa Monastery and local cheese factory\r\nSunrise hike to Kyanjin Ri (4,773m)\r\nSnow landscapes in winter & green valleys in spring\r\nLess crowded than Everest/Annapurna\r\nPerfect mix of adventure + culture + nature', NULL, 'Kathmandu – Syabrubesi – Kathmandu transportation (bus/jeep)\r\nExperienced trekking guide\r\nTeahouse/lodge accommodation during trek\r\n3 meals per day during trekking (Breakfast, Lunch, Dinner)', 'Snacks, soft drinks, chocolates & bottled water\r\nPersonal expenses (extra food, drinks, shopping)\r\nAnything not mentioned in “Package Includes”', '1782920446_testing_pdf.pdf', '1782920446_111887_65ec4e8d1cf2a.jpg', 1, 1, '2026-07-01 15:40:46', 'domestic', 28.2106, 85.5714, 'Langtang'),
(17, 'Mardi Himal Trek', '5 Days', '16500', 300.00, 20000.00, 'The Mardi Himal Trek is one of Nepal\'s \"hidden gems,\" offering a quiet, ridge-top trail with spectacular views of Machhapuchhre (Fishtail) and the Annapurna massif. To fit this into 5 days from Kathmandu, you will need to utilize a flight or a very early private drive to Pokhara on Day 1 to maximize your trekking time.', 'The Ridge Trail: Offers constant 360-degree mountain views because you walk along a high ridge rather than deep in a valley.\r\nFishtail Proximity: Provides the closest possible view of the sacred Mt. Machhapuchhre (Fishtail), which towers directly over High Camp.\r\nBadal Danda: A spectacular viewpoint where you often stand above a \"sea of clouds\" covering the lower valleys.\r\nRhododendron Forests: Features ancient, moss-covered forests that bloom with vibrant red and pink flowers during the spring.\r\nEfficient Altitude: The fastest trek in the Annapurna region to reach 4,500m, making it perfect for a short 5-day trip.', NULL, 'Kathmandu – Pokhara – Kathmandu transportation (Bus)\r\nExperienced licensed trekking guide\r\nTeahouse/lodge accommodation during trek\r\n3 meals per day during trekking (Breakfast, Lunch, Dinner)\r\nTour coordination & support throughout the trip', 'Snacks, soft drinks, chocolates & bottled water\r\nPersonal expenses (extra food, drinks, shopping)\r\nAnything not mentioned in “Package Includes”', '1782921754_testing_pdf.pdf', '1782921754_mardi-himal-trek.jpg', 1, 1, '2026-07-01 16:02:34', 'domestic', 28.7195, 83.9448, 'Mardi Himal High Camp'),
(18, 'Thailand Tour', '4 Nights 5 Days', '70000', 500.00, 90000.00, 'This Thailand holiday blends the lively beach atmosphere of Pattaya with the cultural charm and modern energy of Bangkok. Enjoy a refreshing island escape to Coral Island (Koh Larn), relax by the sea, and explore Thailand’s famous temples, city sights, and shopping spots. With comfortable hotel stays, guided tours, and smooth transfers, this trip offers the perfect balance of sightseeing, leisure, and tropical fun — ideal for couples, families, and holiday travellers looking for a complete Thailand experience.', 'Speedboat tour to Coral Island (Koh Larn)\r\nBangkok Half-Day City Tour with Golden Buddha & Marble Temple\r\nPrivate airport transfers\r\n4 nights hotel stay with daily breakfast\r\nCoral Island lunch included\r\nFree shopping & leisure day in Bangkok\r\nPattaya beach stay + Bangkok city experience', NULL, 'International Flight ( KTM DMK KTM 7 KG HANDCARRY)\r\nAirport Pick Up & Drop on PVT\r\n2 Nights hotel accommodation in Pattaya. ( 4 Star)\r\n2 Nights hotel accommodation in Bangkok. ( 3 Star)\r\nEvery Day Breakfast\r\nPattaya to Bangkok Transfer on PVT\r\nCoral Island tour with Lunch & Transfer\r\nHalf Day City Tour 2 Temples on SIC\r\nVisa fee\r\nAll Tours & Transfers on SIC Basis', 'Meals aside from those specifically included.\r\nCity and Resort Taxes If Applicable\r\nSurcharge If Applicable\r\nPersonal Expenses\r\nTips\r\nAny Other charge which is not mentioned in above inclusions.', '1782923675_testing_pdf.pdf', '1782923675_4600_t8afNwa2.jpg', 1, 1, '2026-07-01 16:34:35', 'international', 15.8700, 100.9925, 'Thailand'),
(19, 'Ghorepani Poon Hill Trek', '5 Days', '10000', 270.00, 12000.00, 'A short and popular trek in the Annapurna region, famous for sunrise views over the Himalayas. The trek passes through beautiful villages, forests, and traditional Gurung communities.', 'Poon Hill sunrise viewpoint\r\nAnnapurna and Dhaulagiri views\r\nGurung culture\r\nRhododendron forests', NULL, 'Guide\r\nAccommodation\r\nTransportation\r\nTrekking permit', 'Personal expenses\r\nInsurance', '1782924293_testing_pdf.pdf', '1782924293_Poon hill.jpg0.60816600 1731412954.webp', 1, 1, '2026-07-01 16:44:53', 'domestic', 28.4000, 81.6900, 'Poon Hill'),
(20, 'Dubai City Tour', '5 Days', '180000', 1350.00, 250000.00, 'Experience Dubai’s modern architecture, desert adventures, shopping, and famous attractions.', 'Burj Khalifa\r\nDesert safari\r\nDubai Mall\r\nMarina cruise', NULL, 'Hotel accommodation\r\nAirport transfers\r\nCity sightseeing\r\nDesert safari\r\nTour guide\r\nTransportation', 'Flight tickets\r\nVisa charges\r\nPersonal expenses\r\nTravel insurance\r\nExtra activities', '1782927319_testing_pdf.pdf', '1782927319_7c70ab1d-5b73-4916-a4a6-3cfdd2e876b7.webp', 1, 1, '2026-07-01 17:35:19', 'international', 25.2048, 55.2708, 'Dubai'),
(21, 'Switzerland Mountain Tour', '7 Days', '300000', 2300.00, 320000.00, 'Switzerland Mountain Tour is a scenic European adventure featuring breathtaking Alpine landscapes, beautiful lakes, charming villages, and world-famous mountain destinations. Experience Swiss culture, mountain railways, and unforgettable views of the Swiss Alps.', 'Swiss Alps mountain views\r\nInterlaken adventure town\r\nJungfrau mountain region\r\nBeautiful Swiss villages\r\nLake Lucerne sightseeing\r\nScenic train journeys\r\nSwiss chocolate and local culture', NULL, 'Hotel accommodation\r\nAirport transfers\r\nTransportation\r\nSightseeing tours\r\nProfessional guide\r\nMountain excursion tickets', 'Flight tickets\r\nVisa fees\r\nTravel insurance\r\nPersonal expenses\r\nExtra activities', '1782927567_testing_pdf.pdf', '1782927567_scl_swiss_alps_switzerland_001_3000x1500_fa1f809c9f21_a5322cf159.webp', 1, 1, '2026-07-01 17:39:27', 'international', 46.8182, 8.2275, 'Switzerland'),
(22, 'Rara Lake Tour', '6 Days', '45000', 340.00, 60000.00, 'Rara Lake Tour takes you to the largest lake of Nepal, located in the remote and peaceful Mugu district. Surrounded by forests, mountains, and beautiful landscapes, Rara offers a perfect escape for nature lovers. The journey provides scenic views, local culture, boating experiences, and a peaceful Himalayan environment away from crowded cities.', 'Visit Nepal’s largest lake\r\nBeautiful Himalayan landscapes\r\nBoating on Rara Lake\r\nExplore Rara National Park\r\nForest walks and nature photography\r\nExperience remote western Nepal culture\r\nPeaceful mountain environment', NULL, 'Transportation\r\nHotel/lodge accommodation\r\nGuide\r\nSightseeing\r\nBoating at Rara Lake\r\nRequired permits', 'Flight tickets (if not included)\r\nPersonal expenses\r\nTravel insurance\r\nExtra activities\r\nMeals outside package', '1782927845_testing_pdf.pdf', '1782927845_5da2ff_a56e03ed850a41c1b2b4f30671b87789~mv2.webp', 1, 1, '2026-07-01 17:44:05', 'domestic', 29.5300, 82.0800, 'Rara Lake');

-- --------------------------------------------------------

--
-- Table structure for table `tour_itineraries`
--

CREATE TABLE `tour_itineraries` (
  `id` int(11) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `day_number` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tour_itineraries`
--

INSERT INTO `tour_itineraries` (`id`, `tour_id`, `day_number`, `title`, `description`) VALUES
(90, 13, 1, 'Arrive at Bali Airport. Transfer to Hotel. Check in. Free Time. Overnight.', 'Arrival in Bali\r\nMeet and greet with our representatives\r\nTransfer to hotel\r\nCheck-in to the hotel\r\nRelax, free time\r\nOvernight in Bali'),
(98, 5, 1, 'Flight from Kathmandu/Manthali to Lukla.', 'Flight time: Approx 40 min from KTM/20 min from Manthali. Trek to Phakding (2,650 m). Trek time: Approx. 3 hrs.'),
(99, 5, 2, 'Trek from Phakding to Namche Bazaar (3,440 m).', 'Trek time: Approx. 6 hrs.'),
(101, 10, 1, 'Drive from Kathmandu via Arughat to Soti Khola (730m / 2896ft)', 'Drive from Kathmandu via Arughat to Soti Khola (730m / 2896ft)'),
(102, 10, 2, 'Trek from Soti Khola (730m / 2896ft) to Machha Khola (890m / 2,965ft)', 'Trek from Soti Khola (730m / 2896ft) to Machha Khola (890m / 2,965ft)'),
(103, 10, 3, ' Trek from Machha Khola (890m / 2,965ft) to Doban (1,070m / 3510ft)', ' Trek from Machha Khola (890m / 2,965ft) to Doban (1,070m / 3510ft)'),
(115, 15, 1, 'Pokhara (Lakeside) ➝ Jhinu Danda ➝ New Bridge ➝ Chhomrong ➝ Sinuwa', 'Early morning scenic jeep drive along Modi Khola valley\r\nCross suspension bridges over roaring river\r\nEnter Annapurna Conservation Area\r\nStone stair climb to Chhomrong\r\nForest trail with rhododendron & bamboo\r\nReach peaceful Upper Sinuwa (2,340m)\r\nOvernight: Upper Sinuwa (2,340m)'),
(116, 15, 2, 'Upper Sinuwa ➝ Deurali', 'Dense bamboo & rhododendron forest\r\nPossible wildlife: Langur monkeys\r\nWalk beside Modi Khola\r\nNarrow gorge landscapes\r\nEnter alpine zone\r\nOvernight: Deurali (3,200m)'),
(117, 15, 3, 'Deurali ➝ Annapurna Base Camp (ABC)', 'Walk through Annapurna Sanctuary\r\nGlacier basin landscapes\r\n360° mountain amphitheatre\r\nArrive: Annapurna Base Camp (4,130m)\r\nOvernight: ABC (4,130m)\r\nVisible Peaks:\r\n* Annapurna I\r\n* Annapurna South\r\n* Hiunchuli\r\n* Machhapuchhre\r\n* Gangapurna'),
(118, 15, 4, 'ABC ➝ Bamboo', 'Morning golden sunrise at ABC\r\nDownhill glacier valley walk\r\nWaterfalls & lush forest return\r\nOvernight: Bamboo'),
(119, 15, 5, 'Bamboo ➝ Jhinu ➝ Drive to Pokhara', 'Final mountain views\r\nOptional Jhinu natural hot spring (if time permits)\r\nScenic jeep ride back\r\nEvening: Return to Pokhara\r\nTrip ends Pokhara evening'),
(162, 16, 1, 'Kathmandu → Trishuli River → Dhunche → Syabrubesi → Bamboo', 'Reach Syabrubesi (1,500m)\r\nStart trek along Langtang Khola river\r\nForest trail, suspension bridges, waterfalls\r\nReach Bamboo\r\nOvernight stay at Bamboo'),
(163, 16, 2, 'Bamboo → Lama Hotel → Ghodatabela → Langtang Village', 'Pine & rhododendron forests\r\nArmy check post at Ghodatabela\r\nValley opens with mountain views\r\nReach Langtang Village (3,430m)\r\nOvernight stay at Langtang Village'),
(164, 16, 3, 'Langtang → Kyanjin Gompa → Kyanjin Ri', 'Wide valley walk with glacier views\r\nReach Kyanjin Gompa (3,870m)\r\nVisit monastery & cheese factory\r\nHike to Kyanjin Ri (4,773m)\r\nSunrise/snow peaks/glaciers\r\nOvernight stay at Kyanjin Gompa'),
(165, 16, 4, 'Kyanjin Gompa → Langtang Village → Ghodatabela', 'Descend through valley\r\nPass Langtang Village\r\nForest trails\r\nReach Ghodatabela\r\nOvernight stay at Ghodatabela'),
(166, 16, 5, 'Ghodatabela → Lama Hotel → Syabrubesi', 'Downhill forest trail\r\nRiver views\r\nReach Syabrubesi\r\nOvernight stay at Syabrubesi'),
(167, 16, 6, 'Syabrubesi → Kathmandu', 'Early breakfast\r\nScenic return drive\r\nTrip ends Kathmandu evening'),
(168, 17, 1, 'Kathmandu → Pokhara → Deurali (2,100m)', 'Morning: Take an early 25-minute flight to Pokhara.\r\n\r\nAfternoon: Drive 1 hour to Kande (the trailhead). Trek 3–4 hours through Australian Camp and Pothana to reach Deurali.\r\n\r\nTerrain: Stone-paved stairs and lush forest trails with views of Annapurna South.'),
(169, 17, 2, 'Deurali → Low Camp (2,970m)', 'Activity: A 5–6 hour hike through deep rhododendron and oak forests.\r\n\r\nTerrain: The trail is a steady forest climb. This section is often quiet and \"mystical\" as sunlight barely hits the forest floor.\r\n\r\nView: First close-up glimpses of Mt. Machhapuchhre (Fishtail) appear as you reach Low Camp.'),
(170, 17, 3, 'Low Camp → High Camp (3,580m)', 'Activity: A 4–5 hour trek along the high ridge.\r\n\r\nHighlight: You pass Badal Danda (Cloud Hill), where the forest ends and 360-degree views begin.\r\n\r\nTerrain: Steep, grassy ridges. You are now walking above the tree line with the mountains directly ahead of you.'),
(171, 17, 4, ' High Camp → Base Camp (4,500m) → Siding (1,700m)', 'Sunrise: Leave at 4:00 AM to reach the Upper Viewpoint (4,200m) or Base Camp (4,500m) for sunrise.\r\n\r\nDescent: After breakfast at High Camp, descend steeply for 6–7 hours to Siding Village.\r\n\r\nTerrain: Very steep downhill through alpine meadows and then back into thick forest.'),
(172, 17, 5, 'Siding → Pokhara → Kathmandu', 'Morning: Take a 2–3 hour local jeep ride from Siding back to Pokhara.\r\n\r\nAfternoon: Fly back to Kathmandu (or take a late afternoon tourist bus if you have more time).'),
(173, 18, 1, 'Arrival in Bangkok Airport. Transfer to Pattaya hotel. Check in to Hotel. Free Time . .Overnight .', 'Meet our Representative in Airport.\r\nPick up from Bangkok Airport Drop to Pattaya Hotel.\r\nCheck in to Hotel.\r\nRest , Free time.\r\nOvernight at hotel'),
(174, 18, 2, 'Breakfast at hotel. Coral Island tour + Lunch . Overnight at Hotel.', 'Breakfast at hotel.\r\nPick up from hotel\r\nProceed to Coral Island tour\r\nKoh Larn Tour\r\nBuffet Lunch at Koh Larn\r\nBack to hotel\r\nOvernight at hotel.'),
(175, 18, 3, 'Breakfast at Hotel, Transfer to Bangkok Hotel + Half Day City Tour of 2 Temple Overnight at hotel.', 'Breakfast at hotel.\r\nCheck out from hotel\r\nTransfer to Bangkok Hotel.\r\nCheck in to hotel.\r\nProceed to City Tour\r\nGolden Temple\r\nMini Reclining Buddha\r\nGems Gallery\r\nOvernight at hotel.'),
(176, 18, 4, 'Breakfast at hotel, Free day +Overnight at hotel.', 'Breakfast at hotel.\r\nFree day\r\nShopping Day\r\nOvernight at Hotel.'),
(177, 18, 5, 'Breakfast at hotel, Transfer to Airport , Departure.', 'Breakfast at hotel\r\nCheck out from hotel.\r\nTransfer to Bangkok\r\nAirport Departure'),
(178, 19, 1, 'Pokhara to Nayapul and trek to Tikhedhunga', 'Drive from Pokhara to Nayapul and begin the trek through beautiful villages, rivers, and green hills. Walk through traditional settlements and reach Tikhedhunga for an overnight stay.'),
(179, 19, 2, 'Tikhedhunga to Ghorepani', 'Continue trekking through rhododendron forests and stone stair trails. Reach Ghorepani village and enjoy amazing views of the surrounding Himalayan peaks.'),
(180, 19, 3, 'Poon Hill Sunrise and Trek to Tadapani', 'Early morning hike to Poon Hill to experience a stunning sunrise over Annapurna and Dhaulagiri ranges. After enjoying the views, continue trekking towards Tadapani.'),
(181, 19, 4, 'Tadapani to Ghandruk', 'Walk through beautiful forests and reach Ghandruk, a famous Gurung village. Explore local culture, traditional houses, and mountain views.'),
(182, 19, 5, 'Ghandruk to Pokhara', 'Descend from Ghandruk to Nayapul and drive back to Pokhara, completing the trek.'),
(183, 20, 1, 'Arrival in Dubai', 'Arrive in Dubai, hotel transfer, and enjoy the city atmosphere.'),
(184, 20, 2, 'Dubai City Tour', 'Visit famous landmarks including Burj Khalifa, Dubai Mall, and modern city attractions.'),
(185, 20, 3, 'Desert Safari Experience', 'Enjoy a desert adventure with cultural activities and sunset views.'),
(186, 20, 4, 'Marina and Shopping Tour', 'Explore Dubai Marina, shopping destinations, and waterfront attractions.'),
(187, 20, 5, 'Departure', 'Airport transfer and completion of Dubai tour.'),
(188, 21, 1, 'Arrival in Zurich and City Exploration', 'Arrive in Zurich and transfer to the hotel. Explore the city’s beautiful streets, old town, lakeside views, and experience the beginning of your Swiss journey.'),
(189, 21, 2, 'Zurich to Lucerne Tour', 'Travel to Lucerne, a beautiful city surrounded by mountains and lakes. Visit famous attractions including Chapel Bridge, Lake Lucerne, and explore the charming old town.'),
(190, 21, 3, 'Lucerne to Interlaken', 'Travel through scenic Swiss landscapes to Interlaken, a famous adventure destination located between Lake Thun and Lake Brienz. Enjoy stunning views of the surrounding Alps.'),
(191, 21, 4, 'Jungfrau Mountain Experience', 'Take a scenic mountain journey to the Jungfrau region. Enjoy breathtaking views of snow-covered peaks, glaciers, and alpine landscapes from one of Switzerland’s most famous mountain areas.'),
(192, 21, 5, 'Interlaken Exploration and Swiss Village Tour', 'Explore beautiful villages around Interlaken including Lauterbrunnen and Grindelwald. Enjoy waterfalls, valleys, and traditional Swiss mountain scenery.'),
(193, 21, 6, 'Return to Zurich and Shopping', 'Travel back to Zurich and enjoy free time for shopping, exploring local markets, and experiencing Swiss food and culture.'),
(194, 21, 7, 'Departure from Switzerland', 'Transfer to Zurich Airport and complete the Switzerland Mountain Tour with unforgettable Alpine memories.'),
(195, 22, 1, 'Kathmandu to Nepalgunj', 'Travel from Kathmandu to Nepalgunj by flight or road. Arrive in Nepalgunj, rest at the hotel, and prepare for the journey to the remote Himalayan region.'),
(196, 22, 2, 'Nepalgunj to Rara Lake', 'Begin the journey towards Rara Lake through scenic mountain roads. Enjoy views of hills, forests, rivers, and remote villages while traveling towards Mugu district. Reach the Rara area and explore the peaceful surroundings.'),
(197, 22, 3, 'Explore Rara Lake and National Park', 'Spend the day exploring the beauty of Rara Lake. Enjoy boating, walk around the lake, capture beautiful mountain views, and experience the natural beauty of Rara National Park.'),
(198, 22, 4, 'Rara Lake to Talcha/Nearby Area', 'Enjoy the morning views of the lake and begin the return journey. Travel through beautiful landscapes, local villages, and mountain trails while enjoying the remote Himalayan scenery.'),
(199, 22, 5, 'Return to Nepalgunj', 'Continue the journey back through the western Nepal countryside. Enjoy views of rivers, hills, and traditional settlements before reaching Nepalgunj.'),
(200, 22, 6, 'Nepalgunj to Kathmandu', 'Return to Kathmandu by flight or road. Complete the Rara Lake adventure with unforgettable memories of Nepal’s natural beauty.');

-- --------------------------------------------------------

--
-- Table structure for table `trip_reviews`
--

CREATE TABLE `trip_reviews` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_reviews`
--

INSERT INTO `trip_reviews` (`id`, `trip_id`, `user_id`, `name`, `rating`, `review`, `status`, `created_at`) VALUES
(1, 5, NULL, 'Kushal Acharya', 5, 'dfds t dgs sdf', 1, '2026-06-12 08:46:13'),
(15, 5, NULL, 'Bipin Chapai', 4, 'dammi package maja aayo!', 0, '2026-06-13 06:13:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(150) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_signin` datetime DEFAULT NULL,
  `last_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `country`, `status`, `created_at`, `last_signin`, `last_update`) VALUES
(11, 'Bipin Chapai', 'bipinchapai2059@gmail.com', '$2y$10$1.KsgzFuLGKMCoySsPxbMuotf.2HwU6xFt4nhFjS/XuzndjhEdEG.', '9745355605', NULL, NULL, 1, '2026-05-21 09:53:35', '2026-07-01 23:37:41', NULL),
(21, 'Kushal', 'acharyakushal629@gmail.com', '$2y$10$NCG1UIvPQYxYa7XBHrk2EeYraKW2.qZly1rGQFZXftKtVUPGASpn2', '9745355605', 'Khairahani-13, Chitwan', 'Nepal', 1, '2026-06-15 17:03:47', '2026-07-01 23:32:01', '2026-07-01 12:57:09');

-- --------------------------------------------------------

--
-- Table structure for table `user_activity`
--

CREATE TABLE `user_activity` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `view_count` int(11) DEFAULT 1,
  `time_spent` int(11) DEFAULT 0,
  `last_viewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activity`
--

INSERT INTO `user_activity` (`id`, `user_id`, `package_id`, `action`, `view_count`, `time_spent`, `last_viewed_at`, `created_at`) VALUES
(1, 21, 5, 'view', 12, 3587, '2026-07-01 08:53:09', '2026-06-19 08:42:50'),
(10, 21, 5, 'book', 1, 0, NULL, '2026-07-01 08:04:30'),
(14, 21, 17, 'view', 6, 776, '2026-07-01 16:36:48', '2026-07-01 16:27:32'),
(19, 21, 18, 'view', 6, 65, '2026-07-01 16:35:49', '2026-07-01 16:35:06'),
(26, 21, 21, 'view', 1, 32, NULL, '2026-07-01 17:48:51'),
(27, 21, 22, 'view', 1, 6, NULL, '2026-07-01 17:49:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_fcm_tokens`
--
ALTER TABLE `admin_fcm_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_admin_token` (`admin_id`,`token`(255));

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bus_inquiries`
--
ALTER TABLE `bus_inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bus_id` (`bus_id`);

--
-- Indexes for table `chatbot_quiries`
--
ALTER TABLE `chatbot_quiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `flights`
--
ALTER TABLE `flights`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_albums`
--
ALTER TABLE `gallery_albums`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `gallery_photos`
--
ALTER TABLE `gallery_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `album_id` (`album_id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inquiry_trip` (`trip_id`);

--
-- Indexes for table `package_bookings`
--
ALTER TABLE `package_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_booking_trip` (`package_id`),
  ADD KEY `fk_booking_user` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recmnd_clicks`
--
ALTER TABLE `recmnd_clicks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_package` (`user_id`,`package_id`),
  ADD KEY `fk_click_package` (`package_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tour_itineraries`
--
ALTER TABLE `tour_itineraries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`);

--
-- Indexes for table `trip_reviews`
--
ALTER TABLE `trip_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `fk_review_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_package` (`user_id`,`package_id`,`action`),
  ADD KEY `fk_activity_package` (`package_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_fcm_tokens`
--
ALTER TABLE `admin_fcm_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bus_inquiries`
--
ALTER TABLE `bus_inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `chatbot_quiries`
--
ALTER TABLE `chatbot_quiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `flights`
--
ALTER TABLE `flights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `gallery_albums`
--
ALTER TABLE `gallery_albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gallery_photos`
--
ALTER TABLE `gallery_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `package_bookings`
--
ALTER TABLE `package_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `recmnd_clicks`
--
ALTER TABLE `recmnd_clicks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tour_itineraries`
--
ALTER TABLE `tour_itineraries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `trip_reviews`
--
ALTER TABLE `trip_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_fcm_tokens`
--
ALTER TABLE `admin_fcm_tokens`
  ADD CONSTRAINT `fk_admin_fcm_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bus_inquiries`
--
ALTER TABLE `bus_inquiries`
  ADD CONSTRAINT `bus_inquiries_ibfk_1` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gallery_photos`
--
ALTER TABLE `gallery_photos`
  ADD CONSTRAINT `gallery_photos_ibfk_1` FOREIGN KEY (`album_id`) REFERENCES `gallery_albums` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `fk_inquiry_trip` FOREIGN KEY (`trip_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `package_bookings`
--
ALTER TABLE `package_bookings`
  ADD CONSTRAINT `fk_booking_trip` FOREIGN KEY (`package_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recmnd_clicks`
--
ALTER TABLE `recmnd_clicks`
  ADD CONSTRAINT `fk_click_package` FOREIGN KEY (`package_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_click_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_itineraries`
--
ALTER TABLE `tour_itineraries`
  ADD CONSTRAINT `tour_itineraries_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trip_reviews`
--
ALTER TABLE `trip_reviews`
  ADD CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `trip_reviews_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD CONSTRAINT `fk_activity_package` FOREIGN KEY (`package_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

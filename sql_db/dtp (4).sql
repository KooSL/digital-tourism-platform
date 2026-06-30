-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2026 at 11:11 AM
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
  `admin_id` int(11) DEFAULT NULL,
  `token` text NOT NULL,
  `device` varchar(50) DEFAULT NULL,
  `last_active` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_fcm_tokens`
--

INSERT INTO `admin_fcm_tokens` (`id`, `admin_id`, `token`, `device`, `last_active`) VALUES
(13, NULL, 'eHqK5TLrUr6nDCRR1cOVs4:APA91bHPyvj8oXm5B1CrZ2JwptEKDF1UetOAHnBswp3d4FE6wU1N7g7Tu0zKoHtZM1_gh7PYkiYvHrsy6ovG9TPlLP7dq0FQ8Tthj_AT0WhpfiikCHYyAqQ', NULL, '2026-03-16 04:05:00'),
(14, NULL, 'eHqK5TLrUr6nDCRR1cOVs4:APA91bE9rrEaJ5BkLm5Uh_ZkLKiuFjdGD0tcBfN0gFbFQxDvq4HfwFMbEfhT0TZKQjy7lm_Ryt9_xxaeSLO38KewEkAr9YJ1_gDo4lnCx38AZ6ApVULdv3I', NULL, '2026-05-14 10:53:01'),
(15, NULL, 'eHqK5TLrUr6nDCRR1cOVs4:APA91bHhs8Yobkt8cYc9cCwL5_A9E6bp7QgaB71EXfdxNLhNTMXKQq-d-2d8XfgKqBHkBGdzKubElhzbDbETD0fa0A1Yp9-Tc7GtH9QDI8Og2xfuULxSAao', NULL, '2026-05-18 14:03:51');

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

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `is_featured`, `status`, `created_at`) VALUES
(3, 'What services does Take Your Seat provide?', 'We offer domestic and international tour packages, flight ticketing, trekking, visa assistance, adventure activities, and customized travel solutions.', 0, 1, '2026-01-17 17:47:54');

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
(102, 5, 'Kushal Comp', 'comp.kushal@gmail.com', '9745355605', 'mnkjbfnfjkdfjkdhfjkd', '2026-03-23 09:47:24');

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
(16, 5, 11, 'Bipin Chapai', 'bipinchapai2059@gmail.com', 'Nepal', '9745355605', '2026-06-10', 1, NULL, 'pending', 'eSewa', 'BOOK_1780920691', 'paid', '2026-06-08 12:12:10');

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

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`) VALUES
(7, 21, '020d912ef714f9ac059e8f396ad0fa398a7a2f1e3b50a66827fb1b54a4c1f045', '2026-06-23 13:49:49');

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
(13, 'Bali', '4 Nights / 5 Days', '115000', 800.00, NULL, 'This 4-night, 5-day Bali itinerary offers a blend of coastal adventure and cultural discovery. Based in the Kuta area, the trip includes water sports like banana boat rides at Tanjung Benoa and a sunset visit to the Uluwatu Temple. You will explore the island\'s interior with a visit to the Kintamani volcano and the artistic heritage of Ubud. A major highlight is a full-day fast boat excursion to Nusa Penida to see iconic landmarks such as Kelingking Beach and Angel\'s Billabong.', 'This 4-night Bali getaway combines Kuta\'s vibrant beaches and water sports with a scenic tour of the Kintamani volcano, the artistic charm of Ubud, and a stunning day trip to the iconic cliffs and shores of Nusa Penida.', NULL, 'International Ticket: KTM-DPS-KTM\r\n4 Nights hotel accommodation in Bali\r\nDaily breakfast at hotel\r\nAirport Transfer (Pick up and drop off)\r\nWatersports at Tanjung Benoa (Banana Boat)\r\nUluwatu Sunset Temple Tour\r\nKintamani Volcano viewpoint tour\r\nUbud Art Village exploration\r\nNusa Penida Island full-day tour with fast boat transfers\r\nLunch in Nusa Penida Island\r\nVisa fees\r\nAll tours and transfers on SIC Basis', 'Meals aside from those specifically included.\r\nCity and Resort Taxes (If Applicable)\r\nSurcharge (If Applicable)\r\nPersonal Expenses\r\nTips\r\nAny Other charge which is not mentioned in above inclusions', '1771841158_Important-Questions (1).pdf', '1771841158_bali-for-digital-nomads.jpg', 1, 1, '2026-02-23 10:05:58', 'international', NULL, NULL, NULL);

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
(103, 10, 3, ' Trek from Machha Khola (890m / 2,965ft) to Doban (1,070m / 3510ft)', ' Trek from Machha Khola (890m / 2,965ft) to Doban (1,070m / 3510ft)');

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
(11, 'Bipin Chapai', 'bipinchapai2059@gmail.com', '$2y$10$1.KsgzFuLGKMCoySsPxbMuotf.2HwU6xFt4nhFjS/XuzndjhEdEG.', '9745355605', NULL, NULL, 1, '2026-05-21 09:53:35', '2026-06-15 22:04:34', NULL),
(21, 'Kushal', 'acharyakushal629@gmail.com', '$2y$10$NCG1UIvPQYxYa7XBHrk2EeYraKW2.qZly1rGQFZXftKtVUPGASpn2', '9745355605', 'Khairahani-13, Chitwan', 'Nepal', 1, '2026-06-15 17:03:47', '2026-06-23 13:17:51', '2026-06-22 14:19:04');

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
(1, 21, 5, 'view', 5, 124, '2026-06-21 11:39:15', '2026-06-19 08:42:50');

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
  ADD KEY `fk_token_admin` (`admin_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `package_bookings`
--
ALTER TABLE `package_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tour_itineraries`
--
ALTER TABLE `tour_itineraries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_fcm_tokens`
--
ALTER TABLE `admin_fcm_tokens`
  ADD CONSTRAINT `fk_token_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

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

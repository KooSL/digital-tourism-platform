-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2026 at 11:04 AM
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
(75, 2, 'dCDBsLo0xu1O66ZOfLIm4X:APA91bFREaiAzETzZzf3pnrLTll_SVAdwRXuD8e-G8nvkW_EBa9Bd2MuPAaRV-AO_ZsseF46snnBOBOuTwdgTzyQzOBIlLyRtImEnV4OyAUKWgfRwlCBCjE', '2026-07-27 06:37:43', '2026-07-27 06:37:43');

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(280) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `author` varchar(100) DEFAULT 'DTP Team',
  `cover_image` varchar(255) DEFAULT NULL,
  `excerpt` varchar(300) DEFAULT NULL,
  `content` longtext NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `meta_title` varchar(70) DEFAULT NULL,
  `meta_description` varchar(160) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `views` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `slug`, `category_id`, `author`, `cover_image`, `excerpt`, `content`, `tags`, `meta_title`, `meta_description`, `meta_keywords`, `canonical_url`, `is_featured`, `status`, `views`, `created_at`, `updated_at`) VALUES
(1, 'Top 10 Packing Tips for Your Nepal Trek', 'top-10-packing-tips-for-your-nepal-trek', 1, 'DTP Team', 'default-blog.jpg', 'Everything you need to pack for a safe, comfortable trek in the Nepal Himalayas — from layers to first-aid essentials.', '<p>Packing right can make or break your trekking experience. Here are our top tips gathered from years of guiding travelers through Nepal.</p><h2>1. Layer Your Clothing</h2><p>Temperatures swing fast at altitude. Pack moisture-wicking base layers, an insulating mid layer, and a waterproof outer shell.</p><h2>2. Don\'t Skip the First-Aid Kit</h2><p>Blister plasters, altitude sickness tablets, and basic medication are non-negotiable.</p><h2>3. Choose the Right Footwear</h2><p>Break in your trekking boots weeks before departure — trust us on this one.</p>', 'packing tips, trekking, nepal, himalaya', 'Top 10 Packing Tips for Your Nepal Trek | DTP Blog', 'Get ready for the Himalayas with our essential packing checklist for trekking in Nepal, covering clothing, gear and safety items.', 'nepal trekking packing list, trekking gear nepal, himalaya packing tips', NULL, 1, 1, 119, '2026-07-11 13:59:12', '2026-07-27 04:02:10'),
(2, 'Everest Base Camp Trek: A Complete Beginner Guide', 'everest-base-camp-trek-complete-beginner-guide', NULL, 'Kushal Acharya', 'default-blog.jpg', 'Planning your first EBC trek? Here is everything about difficulty, permits, cost, and the best season to go.', '<p>The Everest Base Camp trek is one of the most iconic treks in the world. This guide breaks down everything a first-timer needs to know.</p><h2>Best Time to Go</h2><p>March–May and September–November offer the clearest skies and safest trail conditions.</p><h2>Permits Required</h2><p>You will need the Sagarmatha National Park entry permit and the Khumbu Pasang Lhamu Rural Municipality permit.</p><h2>Difficulty Level</h2><p>Moderate to challenging — good fitness and prior acclimatization are strongly recommended.</p>', 'everest base camp, ebc trek, khumbu, nepal trekking', 'Everest Base Camp Trek Guide 2026 | DTP Blog', 'A complete beginner guide to the Everest Base Camp trek — best season, permits, cost, and difficulty level explained.', 'everest base camp trek, ebc trek guide, khumbu trekking permit', NULL, 1, 1, 24, '2026-07-11 13:59:12', '2026-07-11 16:54:02'),
(3, 'Dashain Festival: Nepal\'s Biggest Celebration Explained', 'dashain-festival-nepal-biggest-celebration-explained', 3, 'Bipin Chapai', 'default-blog.jpg', 'Discover the traditions, rituals and best places to experience Dashain, the most important festival in Nepal.', '<p>Dashain is a 15-day festival celebrated across Nepal with family gatherings, tika ceremonies, and vibrant kite flying.</p><h2>Tika and Jamara</h2><p>Elders bless younger family members with a mixture of rice, yogurt and vermillion on the forehead.</p><h2>Best Places to Experience It</h2><p>Kathmandu Durbar Square and rural villages in the hills offer the most authentic Dashain experience.</p>', 'dashain, nepal festival, culture, tika', 'Dashain Festival in Nepal: Traditions & Travel Tips | DTP Blog', 'Learn about Dashain, Nepal biggest festival — its traditions, rituals, and the best places to experience it as a traveler.', 'dashain festival nepal, nepali culture, tika jamara', NULL, 0, 1, 3, '2026-07-11 13:59:12', '2026-07-13 06:05:32'),
(4, 'Pokhara Travel Guide: Lakes, Views & Adventure', 'pokhara-travel-guide-lakes-views-adventure', 4, 'DTP Team', 'default-blog.jpg', 'From Phewa Lake to paragliding over the Annapurna range, here is your complete guide to Pokhara.', '<p>Pokhara is Nepal\'s adventure capital, known for its lakeside charm and mountain views.</p><h2>Top Things to Do</h2><ul><li>Boating on Phewa Lake</li><li>Paragliding from Sarangkot</li><li>Sunrise views of the Annapurna range</li></ul><h2>Where to Stay</h2><p>Lakeside is the most popular area, with options ranging from budget hostels to lakeview resorts.</p>', 'pokhara, annapurna, travel guide, nepal', 'Pokhara Travel Guide 2026: Lakes, Views & Adventure | DTP Blog', 'Your complete Pokhara travel guide covering top attractions, paragliding, lake activities, and where to stay.', 'pokhara travel guide, pokhara things to do, annapurna views', NULL, 0, 1, 8, '2026-07-11 13:59:12', '2026-07-27 07:42:16'),
(5, 'Ultimate Guide to Trekking Permits in Nepal', 'ultimate-guide-trekking-permits-nepal', 5, 'Bipin Chapai', 'default-blog.jpg', 'Everything you need to know about TIMS cards, national park permits, and restricted area permits before your Nepal trek.', '<p>Understanding permits can be confusing for first-time trekkers. This guide breaks down exactly what you need.</p><h2>TIMS Card</h2><p>Required for most independent treks, obtainable through registered agencies.</p><h2>National Park Permits</h2><p>Sagarmatha, Annapurna Conservation Area, and Langtang each have their own entry fees.</p>', 'trekking permits, tims card, nepal trekking, national park', 'Ultimate Guide to Trekking Permits in Nepal | DTP Blog', 'Everything you need to know about TIMS cards, national park permits, and restricted area permits before your Nepal trek.', 'trekking permits, tims card, nepal trekking, national park', NULL, 1, 1, 16, '2025-12-17 10:38:04', '2025-12-20 14:25:13'),
(6, '10 Must-Try Dishes When Visiting Nepal', '10-must-try-dishes-nepal', 6, 'Rabin Gurung', 'default-blog.jpg', 'From momo to dhido, discover the flavors that define Nepali cuisine and where to try them.', '<p>Nepali food is diverse, shaped by geography and culture. Here are ten dishes every visitor should try.</p><h2>Momo</h2><p>Nepal\'s beloved dumplings, filled with meat or vegetables and served with spicy achar.</p><h2>Dal Bhat</h2><p>The staple meal of lentils, rice, and vegetable curry eaten across the country.</p>', 'nepali food, momo, dal bhat, cuisine', '10 Must-Try Dishes When Visiting Nepal | DTP Blog', 'From momo to dhido, discover the flavors that define Nepali cuisine and where to try them.', 'nepali food, momo, dal bhat, cuisine', NULL, 0, 1, 186, '2026-03-19 14:14:58', '2026-07-27 09:01:25'),
(7, 'New Flight Routes Launching from Kathmandu in 2026', 'new-flight-routes-kathmandu-2026', 7, 'Bipin Chapai', 'default-blog.jpg', 'A roundup of newly announced international routes making it easier to reach Nepal this year.', '<p>Several airlines have announced new direct routes to Kathmandu, improving connectivity for travelers.</p><h2>New Routes</h2><p>Direct flights are expanding from Southeast Asia and the Middle East, cutting travel time significantly.</p>', 'flights, kathmandu airport, travel news', 'New Flight Routes Launching from Kathmandu in 2026 | DTP Blog', 'A roundup of newly announced international routes making it easier to reach Nepal this year.', 'flights, kathmandu airport, travel news', NULL, 0, 1, 195, '2025-12-14 03:26:39', '2025-12-28 05:59:47'),
(8, 'Budget Trekking in Nepal: How to Save Without Sacrificing Safety', 'budget-trekking-nepal-save-money', 8, 'Sristi Karki', 'default-blog.jpg', 'Practical tips for trekking Nepal on a budget while still trekking safely and responsibly.', '<p>Trekking doesn\'t have to break the bank. Here\'s how to keep costs down without cutting corners on safety.</p><h2>Choose the Right Season</h2><p>Shoulder seasons often have lower teahouse prices and fewer crowds.</p><h2>Group Up</h2><p>Sharing a guide and porter with other trekkers significantly reduces per-person cost.</p>', 'budget travel, trekking tips, nepal on a budget', 'Budget Trekking in Nepal: How to Save Without Sacrificing Safety | DTP', 'Practical tips for trekking Nepal on a budget while still trekking safely and responsibly.', 'budget travel, trekking tips, nepal on a budget', NULL, 1, 1, 249, '2026-03-17 10:31:01', '2026-03-19 06:56:16'),
(9, 'A Local\'s Guide to Exploring Bhaktapur', 'locals-guide-exploring-bhaktapur', 4, 'Rabin Gurung', 'default-blog.jpg', 'Skip the crowds and explore Bhaktapur like a local with this insider\'s guide to the ancient city.', '<p>Bhaktapur is more than its famous Durbar Square. Here\'s where locals actually go.</p><h2>Pottery Square</h2><p>Watch traditional pottery being made and even try your hand at the wheel.</p>', 'bhaktapur, kathmandu valley, heritage sites', 'A Local\'s Guide to Exploring Bhaktapur | DTP Blog', 'Skip the crowds and explore Bhaktapur like a local with this insider\'s guide to the ancient city.', 'bhaktapur, kathmandu valley, heritage sites', NULL, 1, 1, 20, '2026-05-11 02:53:04', '2026-05-23 09:19:04'),
(10, 'Why Autumn Is the Best Season for Everest Base Camp', 'autumn-best-season-everest-base-camp', 5, 'Rabin Gurung', 'default-blog.jpg', 'Clear skies, stable weather, and stunning visibility make autumn the top choice for EBC trekkers.', '<p>September through November offers some of the most reliable trekking conditions in the Khumbu region.</p><h2>Stable Weather</h2><p>Post-monsoon skies are typically clear, offering the best mountain visibility of the year.</p>', 'everest base camp, autumn trekking, best season', 'Why Autumn Is the Best Season for Everest Base Camp | DTP Blog', 'Clear skies, stable weather, and stunning visibility make autumn the top choice for EBC trekkers.', 'everest base camp, autumn trekking, best season', NULL, 1, 1, 133, '2026-07-15 02:38:04', '2026-07-19 04:01:31'),
(11, 'Paragliding in Pokhara: Everything First-Timers Should Know', 'paragliding-pokhara-first-timers-guide', 5, 'Bipin Chapai', 'default-blog.jpg', 'A complete first-timer\'s guide to tandem paragliding over Phewa Lake and the Annapurna range.', '<p>Paragliding from Sarangkot is one of Pokhara\'s most popular adventure activities. Here\'s what to expect.</p><h2>Best Time to Fly</h2><p>Morning flights typically offer the calmest winds and clearest mountain views.</p>', 'paragliding, pokhara, adventure activities', 'Paragliding in Pokhara: Everything First-Timers Should Know | DTP Blog', 'A complete first-timer\'s guide to tandem paragliding over Phewa Lake and the Annapurna range.', 'paragliding, pokhara, adventure activities', NULL, 1, 1, 179, '2025-12-04 06:12:06', '2025-12-12 09:40:26'),
(12, 'Nepali New Year: How Bisket Jatra Is Celebrated in Bhaktapur', 'bisket-jatra-nepali-new-year-bhaktapur', 3, 'Kushal Acharya', 'default-blog.jpg', 'An inside look at the chariot processions and festivities marking the Nepali New Year in Bhaktapur.', '<p>Bisket Jatra is one of the most dramatic festivals in the Kathmandu Valley, featuring massive chariot pulling.</p><h2>The Chariot Procession</h2><p>Two neighborhoods compete to pull a massive wooden chariot through the streets.</p>', 'bisket jatra, nepali new year, bhaktapur festival', 'Nepali New Year: How Bisket Jatra Is Celebrated in Bhaktapur | DTP Blo', 'An inside look at the chariot processions and festivities marking the Nepali New Year in Bhaktapur.', 'bisket jatra, nepali new year, bhaktapur festival', NULL, 0, 1, 10, '2025-12-07 04:10:59', '2025-12-21 10:35:53'),
(13, 'Digital Nomad Guide: Working Remotely from Bali', 'digital-nomad-guide-working-remotely-bali', 8, 'Sristi Karki', 'default-blog.jpg', 'Coworking spaces, SIM cards, and visa tips for anyone considering a remote work stint in Bali.', '<p>Bali has become a hotspot for digital nomads thanks to affordable living and reliable internet.</p><h2>Coworking Spaces</h2><p>Ubud and Canggu have the highest concentration of nomad-friendly coworking spaces.</p>', 'digital nomad, bali, remote work', 'Digital Nomad Guide: Working Remotely from Bali | DTP Blog', 'Coworking spaces, SIM cards, and visa tips for anyone considering a remote work stint in Bali.', 'digital nomad, bali, remote work', NULL, 0, 1, 219, '2026-03-29 04:09:13', '2026-04-09 10:32:39'),
(14, 'Family Travel: Planning a Kid-Friendly Trip to Chitwan', 'family-travel-kid-friendly-chitwan', 4, 'Kushal Acharya', 'default-blog.jpg', 'How to plan a Chitwan National Park visit that\'s safe, fun, and manageable with young children.', '<p>Chitwan can be a fantastic family destination with the right planning. Here\'s how to make it work with kids.</p><h2>Choosing Activities</h2><p>Canoe rides and short jungle walks tend to be more manageable for young children than full-day jeep safaris.</p>', 'family travel, chitwan, kids travel nepal', 'Family Travel: Planning a Kid-Friendly Trip to Chitwan | DTP Blog', 'How to plan a Chitwan National Park visit that\'s safe, fun, and manageable with young children.', 'family travel, chitwan, kids travel nepal', NULL, 0, 1, 126, '2026-06-15 02:25:50', '2026-06-24 08:26:02');

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Travel Tips', 'travel-tips', '2026-07-11 13:46:14'),
(3, 'Culture & Festivals', 'culture-festivals', '2026-07-11 13:46:14'),
(4, 'Destination Guides', 'destination-guides', '2026-07-11 13:46:14'),
(5, 'Adventure & Trekking', 'adventure-trekking', '2026-04-08 10:29:34'),
(6, 'Food & Cuisine', 'food-cuisine', '2025-10-18 10:15:49'),
(7, 'Travel News', 'travel-news', '2025-12-17 11:05:05'),
(8, 'Budget Travel', 'budget-travel', '2025-07-20 05:21:02');

-- --------------------------------------------------------

--
-- Table structure for table `blog_comments`
--

CREATE TABLE `blog_comments` (
  `id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `comment` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_comments`
--

INSERT INTO `blog_comments` (`id`, `blog_id`, `name`, `email`, `comment`, `status`, `created_at`) VALUES
(1, 1, 'Kushal Acharya', 'acharyakushal629@gmail.com', 'Thank you for this information.', 1, '2026-07-11 16:40:46'),
(2, 1, 'Bipin Chapai', 'bipin@gmail.com', 'Nice nice nice', 0, '2026-07-11 16:43:58'),
(100, 7, 'Prakash Tamang', 'prakash.tamang100@gmail.com', 'Very detailed, appreciate the local insight.', 1, '2026-05-31 01:19:18'),
(101, 7, 'Aarav Adhikari', 'aarav.adhikari101@gmail.com', 'This answered exactly what I was wondering about.', 1, '2026-04-25 05:23:23'),
(102, 7, 'Shyam Adhikari', 'shyam.adhikari102@gmail.com', 'Great write-up, saving this for my trip planning.', 1, '2026-06-15 16:57:07'),
(103, 8, 'Suman Magar', 'suman.magar103@gmail.com', 'Wish I had read this before my last trip!', 0, '2026-05-31 07:11:14'),
(104, 9, 'Sabnam Basnet', 'sabnam.basnet104@gmail.com', 'Do you have any updated info on pricing?', 1, '2026-05-21 00:18:06'),
(105, 9, 'Sushmita Bhattarai', 'sushmita.bhattarai105@gmail.com', 'Great write-up, saving this for my trip planning.', 0, '2026-05-16 00:45:28'),
(106, 9, 'Sristi Rai', 'sristi.rai106@gmail.com', 'Really helpful, thanks for putting this together!', 1, '2026-03-07 15:20:25'),
(107, 11, 'Nirajan Shrestha', 'nirajan.shrestha107@gmail.com', 'This answered exactly what I was wondering about.', 1, '2026-04-06 01:22:12'),
(108, 11, 'Nabin Adhikari', 'nabin.adhikari108@gmail.com', 'Great write-up, saving this for my trip planning.', 1, '2026-03-29 08:14:21'),
(109, 11, 'Bibek Gurung', 'bibek.gurung109@gmail.com', 'Really helpful, thanks for putting this together!', 1, '2026-06-01 16:02:13'),
(110, 12, 'Sunita Gurung', 'sunita.gurung110@gmail.com', 'Wish I had read this before my last trip!', 1, '2026-07-13 07:48:10'),
(111, 12, 'Alina Poudel', 'alina.poudel111@gmail.com', 'This answered exactly what I was wondering about.', 0, '2026-07-06 15:31:29'),
(112, 12, 'Karuna Thapa', 'karuna.thapa112@gmail.com', 'Really helpful, thanks for putting this together!', 1, '2026-02-16 10:21:13'),
(113, 13, 'Sushmita Khadka', 'sushmita.khadka113@gmail.com', 'Really helpful, thanks for putting this together!', 0, '2026-01-28 15:45:16'),
(114, 13, 'Rina Regmi', 'rina.regmi114@gmail.com', 'Really helpful, thanks for putting this together!', 1, '2026-02-10 17:12:01'),
(115, 14, 'Kamal Gurung', 'kamal.gurung115@gmail.com', 'Very detailed, appreciate the local insight.', 1, '2026-05-29 15:57:38'),
(116, 14, 'Deepak Khadka', 'deepak.khadka116@gmail.com', 'This answered exactly what I was wondering about.', 1, '2026-05-06 02:09:54'),
(117, 14, 'Sabnam Shrestha', 'sabnam.shrestha117@gmail.com', 'Do you have any updated info on pricing?', 0, '2026-07-23 15:12:46');

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
(3, 'Chitwan Yatayatn', '7777777', 'Chitwan', 'India', '2026-07-22', '00:00:00', '00:00:00', 5001.00, 50, 'hekloooooooooooooooooo', '1778060024_TTMS (1).jpg', 1, '2026-05-06 09:33:44'),
(100, 'Greenline Yatayat', 'BA-1 PA 8231', 'Kathmandu', 'Pokhara', '2026-08-04', '16:00:00', '00:00:00', 800.00, 40, 'Comfortable AC bus service from Kathmandu to Pokhara.', 'default-bus.jpg', 1, '2026-05-16 15:50:32'),
(101, 'Greenline Yatayat', 'BA-5 PA 6711', 'Kathmandu', 'Chitwan', '2026-09-13', '06:00:00', '13:00:00', 1200.00, 40, 'Comfortable AC bus service from Kathmandu to Chitwan.', 'default-bus.jpg', 1, '2026-05-19 06:42:06'),
(102, 'Greenline Yatayat', 'BA-7 PA 3719', 'Pokhara', 'Chitwan', '2026-09-15', '16:00:00', '00:00:00', 1200.00, 30, 'Comfortable AC bus service from Pokhara to Chitwan.', 'default-bus.jpg', 1, '2026-07-16 15:10:07'),
(103, 'Greenline Yatayat', 'BA-5 PA 9827', 'Kathmandu', 'Butwal', '2026-09-18', '16:00:00', '00:00:00', 1200.00, 30, 'Comfortable AC bus service from Kathmandu to Butwal.', 'default-bus.jpg', 1, '2026-05-10 05:05:13'),
(104, 'Greenline Yatayat', 'BA-7 PA 1971', 'Kathmandu', 'Biratnagar', '2026-08-15', '08:00:00', '13:00:00', 1500.00, 45, 'Comfortable AC bus service from Kathmandu to Biratnagar.', 'default-bus.jpg', 1, '2026-07-06 11:00:18');

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

--
-- Dumping data for table `bus_inquiries`
--

INSERT INTO `bus_inquiries` (`id`, `bus_id`, `travel_date`, `name`, `phone`, `email`, `message`, `created_at`) VALUES
(100, 3, '2026-08-09', 'Roshan Thapa', '9823415412', 'roshan.thapa100@example.com', 'Do you have a night departure option for this route?', '2026-04-02 15:03:04'),
(101, 103, '2026-08-03', 'Nirajan Shrestha', '9836858045', 'nirajan.shrestha101@example.com', 'Can I reschedule my bus ticket if my plans change?', '2026-02-05 08:00:41'),
(102, 102, '2026-10-24', 'Nirajan Bhandari', '9819514426', 'nirajan.bhandari102@example.com', 'Is this bus AC or non-AC? Also does it have charging ports?', '2026-03-22 12:19:44'),
(103, 100, '2026-08-26', 'Aarav Bhattarai', '9847988991', 'aarav.bhattarai103@example.com', 'Can I reschedule my bus ticket if my plans change?', '2026-05-31 01:30:06'),
(104, 100, '2026-07-28', 'Bikash Chhetri', '9813266915', 'bikash.chhetri104@example.com', 'Do you have a night departure option for this route?', '2026-05-27 08:04:43'),
(105, 3, '2026-10-07', 'Amrita Dahal', '9837765555', 'amrita.dahal105@example.com', 'What is the luggage allowance per passenger?', '2026-07-17 04:44:01'),
(106, 102, '2026-08-10', 'Radha Rai', '9822545163', 'radha.rai106@example.com', 'Can I book 4 seats together for a family trip?', '2026-03-14 05:54:32'),
(107, 101, '2026-08-10', 'Alina Bhattarai', '9810151832', 'alina.bhattarai107@example.com', 'Is this bus AC or non-AC? Also does it have charging ports?', '2026-07-20 02:47:35'),
(108, 103, '2026-10-14', 'Namrata Neupane', '9815209093', 'namrata.neupane108@example.com', 'Can I reschedule my bus ticket if my plans change?', '2026-07-14 09:44:25'),
(109, 104, '2026-07-28', 'Bibek Karki', '9811615432', 'bibek.karki109@example.com', 'Can I book 4 seats together for a family trip?', '2026-03-20 14:28:07'),
(110, 104, '2026-10-19', 'Shyam Karki', '9838792913', 'shyam.karki110@example.com', 'Is this bus AC or non-AC? Also does it have charging ports?', '2026-02-21 02:49:33'),
(111, 101, '2026-10-22', 'Rohan Tamang', '9826034380', 'rohan.tamang111@example.com', 'Is this bus AC or non-AC? Also does it have charging ports?', '2026-07-05 11:32:19'),
(112, 101, '2026-09-03', 'Kritika Acharya', '9848670698', 'kritika.acharya112@example.com', 'What is the luggage allowance per passenger?', '2026-01-12 06:15:05'),
(113, 3, '2026-08-02', 'Sunita Bhandari', '9824353384', 'sunita.bhandari113@example.com', 'Is online payment available for bus tickets?', '2026-04-20 14:41:59'),
(114, 103, '2026-10-09', 'Prabin Karki', '9815355936', 'prabin.karki114@example.com', 'Is this bus AC or non-AC? Also does it have charging ports?', '2026-07-12 00:57:43'),
(115, 100, '2026-09-21', 'Prisha Shrestha', '9829687476', 'prisha.shrestha115@example.com', 'Do you have a night departure option for this route?', '2026-05-23 04:31:50'),
(116, 101, '2026-09-10', 'Sita Poudel', '9835655188', 'sita.poudel116@example.com', 'Is this bus AC or non-AC? Also does it have charging ports?', '2026-06-16 14:25:41'),
(117, 104, '2026-09-26', 'Radha Bhandari', '9831875090', 'radha.bhandari117@example.com', 'What is the luggage allowance per passenger?', '2026-05-25 00:41:34');

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
(12, 'bye,goodbye', 'ending conversation', 'Thank you for visiting Digital Tourism Platform. Have a great journey!', 'greeting', 1, '2026-06-20 10:57:54'),
(100, 'refund,cancellation,cancel policy', 'asking about refund or cancellation policy', 'Refunds depend on how early you cancel. Please check our cancellation policy page or contact support for details.', 'policy', 1, '2026-04-28 08:57:01'),
(101, 'group,group booking,group discount', 'asking about group bookings', 'We offer special discounts for groups of 5 or more. Contact us with your group size for a custom quote.', 'booking', 1, '2025-11-29 07:00:17'),
(102, 'honeymoon,couple package,romantic', 'asking about honeymoon packages', 'We have curated honeymoon packages for couples including Bali, Switzerland, and Pokhara getaways.', 'tour', 1, '2025-11-27 16:42:49'),
(103, 'permit,trekking permit,tims', 'asking about trekking permits', 'Most treks require TIMS card and national park permits, which we arrange as part of your package.', 'trekking', 1, '2026-01-21 12:25:51'),
(104, 'altitude sickness,acclimatization,high altitude', 'asking about altitude sickness', 'Our itineraries include acclimatization days, and our guides are trained to recognize and respond to altitude sickness symptoms.', 'trekking', 1, '2025-12-10 13:23:08'),
(105, 'currency,money,exchange rate', 'asking about currency exchange', 'Nepalese Rupee (NPR) is the local currency. Currency exchange counters are available at the airport and major city centers.', 'general', 1, '2026-07-24 03:28:46'),
(106, 'weather,climate,temperature', 'asking about weather conditions', 'Weather varies by season and altitude. We recommend checking your specific trip page for seasonal weather guidance.', 'general', 1, '2026-02-28 12:16:00'),
(107, 'safety,security,emergency', 'asking about safety measures', 'Traveler safety is our top priority - all treks include trained guides, first-aid kits, and emergency evacuation plans.', 'safety', 1, '2025-12-31 02:44:49'),
(108, 'family,kids,children package', 'asking about family packages', 'We offer family-friendly packages with flexible pacing suitable for children. Contact us for age-appropriate recommendations.', 'tour', 1, '2026-07-16 07:11:36'),
(109, 'senior,elderly,senior citizen', 'asking about senior citizen discounts', 'We offer special consideration and discounts for senior citizens on select packages - contact us for details.', 'pricing', 1, '2026-03-13 03:09:20'),
(110, 'student,student discount,id card', 'asking about student discounts', 'Students with a valid student ID can avail discounts on select domestic tour packages.', 'pricing', 1, '2026-05-02 14:46:49'),
(111, 'corporate,company trip,team outing', 'asking about corporate travel', 'We organize corporate retreats and team outings with customized itineraries for companies of any size.', 'booking', 1, '2026-02-14 06:15:15'),
(112, 'blog,articles,travel tips', 'asking about blog or travel articles', 'Check out our Blog section for travel tips, destination guides, and cultural insights about Nepal and beyond.', 'general', 1, '2026-06-05 11:39:56');

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
(100, 'How do I book a tour package?', 'You can book directly through our website by selecting a tour and clicking Book Now, or contact our team for assistance.', 1, 1, '2026-02-25 02:58:08'),
(101, 'What payment methods do you accept?', 'We accept eSewa, bank transfer, and cash payments at our office.', 1, 1, '2026-02-17 03:30:19'),
(102, 'Can I cancel or reschedule my booking?', 'Yes, cancellations and rescheduling are subject to our terms and conditions. Please contact us at least 7 days in advance.', 1, 1, '2026-05-29 05:38:44'),
(103, 'Do you offer group discounts?', 'Yes, we offer 10% off for groups of 5+ and 20% off for groups of 10+ travelers.', 1, 1, '2026-05-16 16:39:26'),
(104, 'Is travel insurance included?', 'Travel insurance is not included by default but can be arranged upon request.', 0, 1, '2026-05-18 12:42:11'),
(105, 'What is the best time to visit Nepal?', 'The best trekking seasons are spring (March-May) and autumn (September-November).', 1, 1, '2025-11-20 05:50:10'),
(106, 'Do I need a visa to visit Nepal?', 'Most travelers can obtain a visa on arrival at Tribhuvan International Airport.', 0, 1, '2025-11-19 09:23:11'),
(107, 'What should I pack for a trekking trip?', 'We recommend layered clothing, sturdy trekking boots, a first-aid kit, and a good sleeping bag.', 0, 1, '2026-02-16 14:54:03'),
(200, 'How do I book a tour package?', 'You can book directly through our website by selecting a tour and clicking Book Now, or contact our team for assistance.', 1, 1, '2026-04-17 07:04:16'),
(201, 'What payment methods do you accept?', 'We accept eSewa, bank transfer, and cash payments at our office.', 1, 1, '2026-04-15 10:02:50'),
(202, 'Can I cancel or reschedule my booking?', 'Yes, cancellations and rescheduling are subject to our terms and conditions. Please contact us at least 7 days in advance.', 1, 1, '2026-07-16 00:19:22'),
(203, 'Do you offer group discounts?', 'Yes, we offer 10% off for groups of 5+ and 20% off for groups of 10+ travelers.', 1, 1, '2026-04-13 13:15:53'),
(204, 'Is travel insurance included?', 'Travel insurance is not included by default but can be arranged upon request.', 0, 1, '2025-10-25 08:50:22'),
(205, 'What is the best time to visit Nepal?', 'The best trekking seasons are spring (March-May) and autumn (September-November).', 1, 1, '2026-05-05 10:37:19'),
(206, 'Do I need a visa to visit Nepal?', 'Most travelers can obtain a visa on arrival at Tribhuvan International Airport.', 0, 1, '2026-06-04 02:02:11'),
(207, 'What should I pack for a trekking trip?', 'We recommend layered clothing, sturdy trekking boots, a first-aid kit, and a good sleeping bag.', 0, 1, '2026-01-27 14:12:01'),
(208, 'Do you provide airport pickup for international packages?', 'Yes, airport pickup and drop off is included for all our international tour packages.', 0, 1, '2025-12-07 03:36:06'),
(209, 'Is altitude sickness a concern on trekking packages?', 'Yes, especially above 3,000m. Our itineraries include acclimatization days and our guides are trained to recognize early symptoms.', 1, 1, '2026-05-10 12:04:56'),
(210, 'Can I get a refund if I cancel my booking?', 'Refunds depend on how far in advance you cancel - please see our cancellation policy for the exact percentage returned.', 0, 1, '2025-11-28 15:20:58'),
(211, 'Do you offer student discounts?', 'Yes, students with a valid ID can get a discount on select domestic packages.', 0, 1, '2026-02-05 10:45:57'),
(212, 'What currency should I bring for domestic trips?', 'Nepalese Rupees are recommended for domestic trips, though major hotels also accept cards.', 0, 1, '2026-05-23 03:48:36'),
(213, 'How far in advance should I book a trekking package?', 'We recommend booking at least 3-4 weeks in advance, especially during peak trekking seasons.', 1, 1, '2026-03-21 16:39:13');

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

--
-- Dumping data for table `flights`
--

INSERT INTO `flights` (`id`, `from_city`, `to_city`, `image`, `description`, `price`, `is_group_fare`, `status`, `created_at`) VALUES
(13, 'Kathmandu', 'Dubai', '1782955865_731524524_1369381711714279_7473082006842661974_n (1).jpg', 'dubai kathmandu dfdflidhkfdj', NULL, 1, 1, '2026-07-02 01:31:05'),
(100, 'Kathmandu', 'Doha', 'default-flight.jpg', 'Kathmandu to Doha flight booking assistance with best fare options.', NULL, 0, 1, '2026-04-27 00:46:08'),
(101, 'Kathmandu', 'Bangkok', 'default-flight.jpg', 'Kathmandu to Bangkok flight booking assistance with best fare options.', NULL, 1, 1, '2026-04-20 16:56:31'),
(102, 'Kathmandu', 'Delhi', 'default-flight.jpg', 'Kathmandu to Delhi flight booking assistance with best fare options.', NULL, 1, 1, '2026-02-02 15:41:45'),
(103, 'Kathmandu', 'Kuala Lumpur', 'default-flight.jpg', 'Kathmandu to Kuala Lumpur flight booking assistance with best fare options.', NULL, 0, 1, '2026-03-24 05:20:36'),
(104, 'Kathmandu', 'Singapore', 'default-flight.jpg', 'Kathmandu to Singapore flight booking assistance with best fare options.', NULL, 1, 1, '2026-01-14 07:33:02'),
(105, 'Kathmandu', 'Istanbul', 'default-flight.jpg', 'Kathmandu to Istanbul flight booking assistance with best fare options.', NULL, 1, 1, '2026-05-31 09:25:49');

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
(4, 'Kathmandu Tour hello', 'kathmandu-tour-hello', '6a6606c0dfa56_WhatsApp Image 2026-01-21 at 9.12.58 PM.jpeg', NULL, 1, '2025-12-25 09:22:10');

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

--
-- Dumping data for table `gallery_photos`
--

INSERT INTO `gallery_photos` (`id`, `album_id`, `image`, `caption`, `created_at`) VALUES
(24, 4, '6a6606d79b780_7c70ab1d-5b73-4916-a4a6-3cfdd2e876b7.webp', NULL, '2026-07-26 13:08:39'),
(25, 4, '6a6606d79dc09_Poonhill.jpg0.608166001731412954.webp', NULL, '2026-07-26 13:08:39'),
(26, 4, '6a6606d79e7eb_4600_t8afNwa2.jpg', NULL, '2026-07-26 13:08:39');

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
(114, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'vvvvvvvvvvvvvv', '2026-07-01 05:32:12'),
(115, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'vvvvvvvvvvvvvv', '2026-07-01 05:33:25'),
(116, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'mmmmmmmmmm', '2026-07-01 05:39:01'),
(117, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'mmmmmmmmmm', '2026-07-01 06:11:23'),
(118, 5, 'Kushal Acharya', 'comp.kushal@gmail.com', '9745355605', 'mmmmmmmmmm', '2026-07-01 06:18:35'),
(119, 5, 'Bipin Chapai', 'bipinchapai2059@gmail.com', '9745355644', 'vx dsfsdg dfgdfg', '2026-07-01 06:20:14'),
(120, 22, 'Kushal Acharya', 'for.triel7@gmail.com', '16142642074', 'fdfndjkfnjkdnfjkd', '2026-07-08 02:49:11'),
(121, 22, 'Kushal Acharya', 'for.triel7@gmail.com', '16142642074', 'dfdfdfdfdf', '2026-07-08 02:52:04'),
(122, 22, 'Kushal Acharya', 'for.triel7@gmail.com', '16142642074', 'hdkjhdsjkghdfjkg', '2026-07-08 02:52:34'),
(200, 19, 'Rajesh Rai', 'rajesh200@example.com', '9830130957', 'Is airport pickup included in this package?', '2026-07-11 02:40:24'),
(201, 27, 'Nisha Basnet', 'nisha201@example.com', '9813765809', 'Can you tell me more about the group discount for 8 people?', '2026-05-01 02:46:54'),
(202, 28, 'Kiran Bhattarai', 'kiran202@example.com', '9832231194', 'Is airport pickup included in this package?', '2025-10-30 01:29:13'),
(203, 30, 'Deepak Sharma', 'deepak203@example.com', '9814989413', 'Can we customize the itinerary for a family with kids?', '2025-10-22 01:26:59'),
(204, 22, 'Aarav Rai', 'aarav204@example.com', '9849395326', 'What is the best season to do this trip?', '2026-01-05 02:34:10'),
(205, 19, 'Arjun Lama', 'arjun205@example.com', '9836115857', 'What vaccinations are required before this trip?', '2026-01-12 05:05:44'),
(206, 13, 'Prakash Khadka', 'prakash206@example.com', '9833210238', 'Can you tell me more about the group discount for 8 people?', '2026-06-07 13:29:53'),
(207, 13, 'Suman Magar', 'suman207@example.com', '9849970620', 'Is there a payment plan available for this package?', '2026-02-10 00:55:17'),
(208, 29, 'Sabina Rai', 'sabina208@example.com', '9833878085', 'Is there a payment plan available for this package?', '2025-12-18 05:58:37'),
(209, 27, 'Bikash Bhattarai', 'bikash209@example.com', '9829822581', 'Do you provide travel insurance as part of the package?', '2026-06-20 02:32:09'),
(210, 27, 'Rajesh Bhattarai', 'rajesh210@example.com', '9820353346', 'Is there a payment plan available for this package?', '2026-02-15 11:21:05'),
(211, 5, 'Kritika Basnet', 'kritika211@example.com', '9834153654', 'Can we customize the itinerary for a family with kids?', '2026-06-05 04:20:11'),
(212, 28, 'Bishal Adhikari', 'bishal212@example.com', '9847227971', 'Is there a payment plan available for this package?', '2026-06-05 00:20:22'),
(213, 32, 'Bikash Magar', 'bikash213@example.com', '9831772150', 'Is there a payment plan available for this package?', '2026-07-21 09:41:24'),
(214, 13, 'Sabnam Chhetri', 'sabnam214@example.com', '9847549861', 'Do you provide travel insurance as part of the package?', '2025-10-08 16:25:43'),
(215, 27, 'Anjali Tamang', 'anjali215@example.com', '9828046846', 'Can we customize the itinerary for a family with kids?', '2026-03-12 15:24:04'),
(216, 17, 'Puja Thapa', 'puja216@example.com', '9838259699', 'Can we customize the itinerary for a family with kids?', '2025-11-22 02:38:16'),
(217, 19, 'Sabnam Poudel', 'sabnam217@example.com', '9843272738', 'Do you provide travel insurance as part of the package?', '2025-12-05 03:23:19'),
(218, 5, 'Manoj Shrestha', 'manoj218@example.com', '9835493983', 'What vaccinations are required before this trip?', '2025-12-14 10:42:52'),
(219, 16, 'Kritika Shrestha', 'kritika219@example.com', '9823478915', 'How many people are typically in a group for this tour?', '2026-02-17 05:40:20'),
(220, 21, 'Sabnam Khadka', 'sabnam220@example.com', '9842950942', 'Do you provide travel insurance as part of the package?', '2026-02-11 12:32:52'),
(221, 27, 'Rina Gurung', 'rina221@example.com', '9847839367', 'Do you provide travel insurance as part of the package?', '2025-10-21 05:58:49'),
(222, 32, 'Aarav Khadka', 'aarav222@example.com', '9840978942', 'Do you provide travel insurance as part of the package?', '2025-12-15 10:08:44'),
(223, 13, 'Sandip Lama', 'sandip223@example.com', '9837457027', 'How many people are typically in a group for this tour?', '2026-05-17 09:30:16'),
(224, 16, 'Rajesh Karki', 'rajesh224@example.com', '9835299425', 'Is airport pickup included in this package?', '2025-12-10 15:52:25'),
(300, 16, 'Arjun Basnet', 'arjun.basnet300@example.com', '9849061124', 'Hi, I wanted to ask what vaccinations or permits are required before departure? Thank you!', '2026-02-14 04:34:58'),
(301, 20, 'Kritika Basnet', 'kritika.basnet301@example.com', '9836273199', 'Good day, how many people are usually in a group for this departure? Looking forward to your reply.', '2026-07-11 10:39:09'),
(302, 21, 'Manoj Chhetri', 'manoj.chhetri302@example.com', '9819993967', 'Hello, quick question - how many people are usually in a group for this departure? Thanks in advance!', '2026-04-24 13:36:43'),
(303, 27, 'Sunita Karki', 'sunita.karki303@example.com', '9845184660', 'Hi, I wanted to ask does this package include airport pickup and drop off? Please let me know soon.', '2025-10-31 02:33:31'),
(304, 26, 'Manoj Acharya', 'manoj.acharya304@example.com', '9828790403', 'Hi, I wanted to ask do you offer a payment plan instead of paying the full amount upfront? Thanks in advance!', '2026-02-22 02:27:08'),
(305, 30, 'Sarita Gurung', 'sarita.gurung305@example.com', '9848930561', 'Good day, can the itinerary be adjusted to add an extra rest day?', '2025-09-24 03:15:22'),
(306, 18, 'Rina Poudel', 'rina.poudel306@example.com', '9833503527', 'Hello, quick question - is travel insurance included, or do we need to arrange it separately? Thanks in advance!', '2025-12-09 15:30:21'),
(307, 26, 'Nabin Neupane', 'nabin.neupane307@example.com', '9840533375', 'Hello, quick question - is there a discount available for a group of 6 people? Please let me know soon.', '2026-06-09 04:05:10'),
(308, 27, 'Bikash Regmi', 'bikash.regmi308@example.com', '9848872036', 'Hey there, does this package include airport pickup and drop off? Thanks in advance!', '2026-06-08 13:56:44'),
(309, 16, 'Nabin Khadka', 'nabin.khadka309@example.com', '9820998093', 'Hey there, is single-room accommodation available for an extra cost? Please let me know soon.', '2026-01-24 05:57:05'),
(310, 22, 'Kritika Adhikari', 'kritika.adhikari310@example.com', '9816309118', 'Hi, I wanted to ask do you offer a payment plan instead of paying the full amount upfront? Please let me know soon.', '2026-06-03 07:22:09'),
(311, 30, 'Sunita Poudel', 'sunita.poudel311@example.com', '9841394450', 'Good day, how many people are usually in a group for this departure?', '2026-03-24 05:51:34'),
(312, 10, 'Manoj Magar', 'manoj.magar312@example.com', '9837093088', 'Namaste, is travel insurance included, or do we need to arrange it separately? Please let me know soon.', '2025-10-16 06:23:58'),
(313, 19, 'Rohan Sharma', 'rohan.sharma313@example.com', '9817097077', 'Namaste, how many people are usually in a group for this departure? Looking forward to your reply.', '2026-06-30 16:05:50'),
(314, 18, 'Kritika Adhikari', 'kritika.adhikari314@example.com', '9812075009', 'Hello, quick question - is there a discount available for a group of 6 people? Looking forward to your reply.', '2025-12-22 12:54:33'),
(315, 15, 'Anjali Dahal', 'anjali.dahal315@example.com', '9824604483', 'Good day, is single-room accommodation available for an extra cost? Thanks in advance!', '2026-03-30 07:53:49'),
(316, 31, 'Namrata Poudel', 'namrata.poudel316@example.com', '9816581724', 'Hi, I wanted to ask can the itinerary be adjusted to add an extra rest day? Thanks in advance!', '2026-07-06 06:54:49'),
(317, 17, 'Radha Chhetri', 'radha.chhetri317@example.com', '9849719249', 'Good day, what is the best season to do this particular trip? Thanks in advance!', '2026-04-25 00:35:59'),
(318, 28, 'Kamal Shrestha', 'kamal.shrestha318@example.com', '9820149174', 'Hello, quick question - what vaccinations or permits are required before departure?', '2026-02-01 04:28:12'),
(319, 19, 'Nirajan Gurung', 'nirajan.gurung319@example.com', '9843375139', 'Good day, is there a discount available for a group of 6 people? Thanks in advance!', '2025-10-31 11:13:04'),
(320, 13, 'Sandip Tamang', 'sandip.tamang320@example.com', '9833434380', 'Hello, quick question - does this package include airport pickup and drop off? Please let me know soon.', '2025-10-02 06:06:31'),
(321, 30, 'Prisha Chhetri', 'prisha.chhetri321@example.com', '9849619838', 'Hello, quick question - is travel insurance included, or do we need to arrange it separately? Please let me know soon.', '2026-05-04 13:39:52'),
(322, 31, 'Prabin Rai', 'prabin.rai322@example.com', '9814565546', 'Good day, is single-room accommodation available for an extra cost?', '2026-03-20 07:30:12'),
(323, 29, 'Pratima Gurung', 'pratima.gurung323@example.com', '9836307554', 'Namaste, can the itinerary be adjusted to add an extra rest day? Thank you!', '2026-01-06 11:07:24'),
(324, 27, 'Sushmita Bhandari', 'sushmita.bhandari324@example.com', '9838628085', 'Hi, I wanted to ask can the itinerary be adjusted to add an extra rest day? Please let me know soon.', '2026-02-21 00:34:31'),
(325, 5, 'Sandip Bhandari', 'sandip.bhandari325@example.com', '9830096294', 'Hi, I wanted to ask do you offer a payment plan instead of paying the full amount upfront? Thank you!', '2025-12-05 04:36:34'),
(326, 18, 'Karuna Bhandari', 'karuna.bhandari326@example.com', '9812185664', 'Hi, I wanted to ask what is the best season to do this particular trip? Thank you!', '2026-02-28 10:20:17'),
(327, 17, 'Sabina Rai', 'sabina.rai327@example.com', '9824517143', 'Hey there, are children allowed on this package, and is there an age limit?', '2026-07-06 13:07:57'),
(328, 17, 'Kritika Khadka', 'kritika.khadka328@example.com', '9821235105', 'Hey there, is travel insurance included, or do we need to arrange it separately? Please let me know soon.', '2026-04-04 12:11:52'),
(329, 27, 'Alina Bhandari', 'alina.bhandari329@example.com', '9822713615', 'Good day, do you offer a payment plan instead of paying the full amount upfront? Please let me know soon.', '2026-05-05 12:48:00'),
(330, 5, 'Shyam Regmi', 'shyam.regmi330@example.com', '9835279091', 'Good day, is there a discount available for a group of 6 people?', '2026-05-19 08:57:26'),
(331, 13, 'Dipesh Adhikari', 'dipesh.adhikari331@example.com', '9829853103', 'Namaste, is single-room accommodation available for an extra cost? Please let me know soon.', '2026-01-23 09:57:45'),
(332, 27, 'Sristi Khadka', 'sristi.khadka332@example.com', '9811207512', 'Namaste, does this package include airport pickup and drop off? Thank you!', '2026-06-28 03:50:24'),
(333, 29, 'Kiran Acharya', 'kiran.acharya333@example.com', '9819193419', 'Namaste, do you offer a payment plan instead of paying the full amount upfront? Thanks in advance!', '2026-07-24 08:24:12'),
(334, 31, 'Shyam Dahal', 'shyam.dahal334@example.com', '9828848968', 'Hi, I wanted to ask are children allowed on this package, and is there an age limit? Looking forward to your reply.', '2026-03-26 10:04:34');

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
(19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'canceled', 'eSewa', '', 'paid', '2026-07-26 16:56:32'),
(200, 15, 101, 'Kiran Magar', 'kiran200@example.com', 'UK', '9820628578', '2026-07-09', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1495475899', 'paid', '2026-07-19 13:20:14'),
(201, 15, 109, 'Sabina Chhetri', 'sabina201@example.com', 'Australia', '9844149822', '2026-04-30', 2, NULL, 'pending', 'eSewa', 'BOOK_1155308128', 'failed', '2025-11-21 15:30:29'),
(202, 27, 112, 'Karuna Magar', 'karuna202@example.com', 'USA', '9814684875', '2026-10-30', 2, NULL, 'pending', 'eSewa', 'BOOK_1286874458', 'failed', '2025-12-26 11:14:50'),
(203, 5, 118, 'Sabnam Thapa', 'sabnam203@example.com', 'Canada', '9819972069', '2026-07-02', 5, NULL, 'pending', 'eSewa', 'BOOK_1405026213', 'failed', '2025-11-21 11:36:35'),
(204, 29, 120, 'Anisha Rai', 'anisha204@example.com', 'UK', '9848372690', '2026-01-27', 4, NULL, 'pending', 'eSewa', 'BOOK_1757232668', 'pending', '2025-12-28 01:35:47'),
(205, 27, 124, 'Maya Bhattarai', 'maya205@example.com', 'USA', '9843240942', '2025-12-03', 1, NULL, 'pending', 'eSewa', 'BOOK_1107064620', 'pending', '2025-11-11 11:10:06'),
(206, 31, 129, 'Aarav Khadka', 'aarav206@example.com', 'USA', '9837513199', '2025-11-07', 2, NULL, 'pending', 'eSewa', 'BOOK_1697756796', 'pending', '2025-11-28 08:36:39'),
(207, 13, 121, 'Anisha Poudel', 'anisha207@example.com', 'Germany', '9831250770', '2026-12-20', 5, NULL, 'confirmed', 'eSewa', 'BOOK_1677703980', 'paid', '2025-10-22 01:54:04'),
(208, 21, 114, 'Sabnam Gurung', 'sabnam208@example.com', 'Germany', '9816606406', '2026-05-15', 1, NULL, 'confirmed', 'eSewa', 'BOOK_1348294647', 'paid', '2026-05-02 10:12:01'),
(209, 10, 118, 'Rina Shrestha', 'rina209@example.com', 'Germany', '9819769023', '2026-06-28', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1654436820', 'paid', '2025-12-28 05:25:11'),
(210, 27, 115, 'Sabina Chhetri', 'sabina210@example.com', 'USA', '9825581902', '2026-08-22', 5, NULL, 'confirmed', 'eSewa', 'BOOK_1965195684', 'paid', '2026-03-18 14:31:42'),
(211, 29, 118, 'Maya Adhikari', 'maya211@example.com', 'USA', '9814956788', '2027-01-26', 5, NULL, 'confirmed', 'eSewa', 'BOOK_1490523712', 'paid', '2026-01-31 09:55:27'),
(212, 21, 112, 'Manoj Lama', 'manoj212@example.com', 'Canada', '9817157379', '2026-04-13', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1023557463', 'paid', '2025-10-07 11:51:18'),
(213, 27, 117, 'Aarav Magar', 'aarav213@example.com', 'Nepal', '9843334803', '2026-11-01', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1666676815', 'paid', '2026-03-31 11:29:40'),
(214, 20, 108, 'Kiran Gurung', 'kiran214@example.com', 'Nepal', '9830732134', '2025-10-17', 5, NULL, 'confirmed', 'eSewa', 'BOOK_1976705430', 'paid', '2025-10-03 12:01:08'),
(215, 21, 120, 'Sabnam Karki', 'sabnam215@example.com', 'USA', '9823475110', '2026-11-06', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1891713190', 'paid', '2025-10-23 11:48:32'),
(216, 17, 116, 'Karuna Lama', 'karuna216@example.com', 'Canada', '9829806607', '2026-11-15', 3, NULL, 'confirmed', 'eSewa', 'BOOK_1923568933', 'paid', '2026-05-29 14:19:09'),
(217, 27, 123, 'Bikash Bhattarai', 'bikash217@example.com', 'Germany', '9810934065', '2026-07-01', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1627666622', 'paid', '2026-05-24 14:38:43'),
(218, 27, 123, 'Prisha Poudel', 'prisha218@example.com', 'UK', '9841640277', '2026-08-13', 1, NULL, 'confirmed', 'eSewa', 'BOOK_1695411453', 'paid', '2025-10-12 11:13:39'),
(219, 13, 129, 'Karuna Khadka', 'karuna219@example.com', 'Australia', '9837400271', '2025-12-10', 1, NULL, 'confirmed', 'eSewa', 'BOOK_1104359116', 'paid', '2026-07-03 01:34:31'),
(220, 19, 108, 'Manoj Basnet', 'manoj220@example.com', 'UAE', '9846251704', '2026-07-27', 4, NULL, 'pending', 'eSewa', 'BOOK_1661051532', 'pending', '2026-05-08 13:56:06'),
(221, 28, 117, 'Sita Khadka', 'sita221@example.com', 'UAE', '9824580721', '2026-05-15', 5, NULL, 'confirmed', 'eSewa', 'BOOK_1584615760', 'paid', '2026-03-28 11:21:43'),
(222, 26, 103, 'Manoj Thapa', 'manoj222@example.com', 'UK', '9818197999', '2025-11-15', 5, NULL, 'pending', 'eSewa', 'BOOK_1022932394', 'failed', '2025-08-21 06:56:40'),
(223, 10, 121, 'Sunita Tamang', 'sunita223@example.com', 'UK', '9814604349', '2026-01-14', 6, NULL, 'confirmed', 'eSewa', 'BOOK_1352788129', 'paid', '2025-09-29 07:07:55'),
(224, 16, 100, 'Deepak Lama', 'deepak224@example.com', 'USA', '9818723624', '2026-02-05', 6, NULL, 'confirmed', 'eSewa', 'BOOK_1141522640', 'paid', '2026-04-28 03:57:55'),
(225, 5, 122, 'Ritu Bhattarai', 'ritu225@example.com', 'UK', '9849517250', '2025-10-08', 3, NULL, 'pending', 'eSewa', 'BOOK_1564880035', 'pending', '2026-04-28 08:18:08'),
(226, 15, 104, 'Sabina Basnet', 'sabina226@example.com', 'UAE', '9844442816', '2025-11-24', 10, NULL, 'confirmed', 'eSewa', 'BOOK_1780871219', 'paid', '2025-12-07 16:29:39'),
(227, 31, 119, 'Bishal Poudel', 'bishal227@example.com', 'Nepal', '9814081923', '2026-12-07', 5, NULL, 'pending', 'eSewa', 'BOOK_1764935082', 'pending', '2026-01-02 13:58:06'),
(228, 29, 104, 'Dipesh Gurung', 'dipesh228@example.com', 'UAE', '9819954514', '2025-12-03', 1, NULL, 'pending', 'eSewa', 'BOOK_1316644047', 'failed', '2026-03-08 10:39:38'),
(229, 29, 127, 'Prisha Bhattarai', 'prisha229@example.com', 'India', '9846999480', '2026-05-08', 2, NULL, 'pending', 'eSewa', 'BOOK_1428133274', 'pending', '2025-12-07 07:41:21'),
(230, 28, 106, 'Suman Karki', 'suman230@example.com', 'UAE', '9827105364', '2027-01-31', 3, NULL, 'confirmed', 'eSewa', 'BOOK_1100105056', 'paid', '2026-05-09 15:19:05'),
(231, 28, 106, 'Sabnam Khadka', 'sabnam231@example.com', 'UAE', '9818731900', '2025-10-30', 6, NULL, 'pending', 'eSewa', 'BOOK_1379673044', 'pending', '2025-09-29 10:57:07'),
(232, 28, 103, 'Kritika Magar', 'kritika232@example.com', 'Australia', '9833598576', '2026-07-22', 1, NULL, 'pending', 'eSewa', 'BOOK_1240781088', 'pending', '2025-11-09 06:24:42'),
(233, 15, 122, 'Anisha Adhikari', 'anisha233@example.com', 'UAE', '9817709822', '2026-07-20', 2, NULL, 'pending', 'eSewa', 'BOOK_1658819288', 'failed', '2026-04-02 14:09:35'),
(234, 32, 101, 'Sarita Chhetri', 'sarita234@example.com', 'Australia', '9811943015', '2026-02-16', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1934007971', 'paid', '2026-02-18 10:37:00'),
(235, 16, 125, 'Bikash Tamang', 'bikash235@example.com', 'Nepal', '9816157753', '2026-01-18', 6, NULL, 'confirmed', 'eSewa', 'BOOK_1397341481', 'paid', '2026-01-15 13:44:21'),
(236, 21, 120, 'Yogesh Magar', 'yogesh236@example.com', 'India', '9813530518', '2025-12-19', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1475788930', 'paid', '2025-09-13 01:58:05'),
(237, 28, 21, 'Sarita Basnet', 'sarita237@example.com', 'Germany', '9828331916', '2026-10-21', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1304133583', 'paid', '2025-11-06 03:37:27'),
(238, 30, 119, 'Sita Rai', 'sita238@example.com', 'Germany', '9813676355', '2026-01-12', 1, NULL, 'confirmed', 'eSewa', 'BOOK_1310791488', 'paid', '2026-02-22 07:04:08'),
(239, 22, 107, 'Aarav Basnet', 'aarav239@example.com', 'Germany', '9821790678', '2026-04-12', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1077390472', 'paid', '2025-10-27 07:47:35'),
(400, 29, 227, 'Kiran Adhikari', 'kiran.adhikari400@example.com', 'Nepal', '9816567730', '2026-04-16', 4, NULL, 'confirmed', 'eSewa', 'BOOK_1488761347', 'paid', '2026-01-03 05:31:54'),
(401, 5, 102, 'Sandip Regmi', 'sandip.regmi401@example.com', 'Nepal', '9822285087', '2026-07-12', 10, NULL, 'confirmed', 'eSewa', 'BOOK_1114224676', 'paid', '2025-06-23 00:39:53'),
(402, 10, 216, 'Sujata Karki', 'sujata.karki402@example.com', 'UK', '9823409124', '2026-08-02', 6, NULL, 'pending', 'eSewa', 'BOOK_1549961072', 'pending', '2026-06-05 14:49:13'),
(403, 5, 103, 'Sabnam Regmi', 'sabnam.regmi403@example.com', 'Germany', '9837538031', '2026-11-21', 12, NULL, 'pending', 'eSewa', 'BOOK_1131420231', 'pending', '2026-04-10 05:40:32'),
(404, 26, 103, 'Prisha Adhikari', 'prisha.adhikari404@example.com', 'UAE', '9835624626', '2025-10-12', 4, NULL, 'confirmed', 'eSewa', 'BOOK_1749743151', 'paid', '2026-07-20 02:41:58'),
(405, 26, 100, 'Prakash Rai', 'prakash.rai405@example.com', 'Australia', '9830367230', '2026-09-25', 12, NULL, 'confirmed', 'eSewa', 'BOOK_1176666339', 'paid', '2025-10-30 08:06:25'),
(406, 16, 204, 'Amrita Karki', 'amrita.karki406@example.com', 'Japan', '9847718799', '2026-03-30', 12, NULL, 'pending', 'eSewa', 'BOOK_1853900508', 'pending', '2026-05-13 11:57:40'),
(407, 28, 229, 'Arjun Neupane', 'arjun.neupane407@example.com', 'Nepal', '9818399753', '2026-08-09', 5, NULL, 'confirmed', 'eSewa', 'BOOK_1457526186', 'paid', '2026-03-31 09:00:24'),
(408, 17, 230, 'Aarav Adhikari', 'aarav.adhikari408@example.com', 'Germany', '9826439472', '2026-06-16', 10, NULL, 'confirmed', 'eSewa', 'BOOK_1091720645', 'paid', '2026-02-12 15:46:27'),
(409, 26, 209, 'Sarita Bhattarai', 'sarita.bhattarai409@example.com', 'India', '9815723039', '2026-07-09', 8, NULL, 'confirmed', 'eSewa', 'BOOK_1016090927', 'paid', '2026-05-16 17:08:22'),
(410, 5, 213, 'Nabin Magar', 'nabin.magar410@example.com', 'UAE', '9816812350', '2026-01-04', 8, NULL, 'confirmed', 'eSewa', 'BOOK_1163930211', 'paid', '2026-03-29 06:04:28'),
(411, 18, 225, 'Sujata Shrestha', 'sujata.shrestha411@example.com', 'South Korea', '9816067030', '2027-02-23', 10, NULL, 'confirmed', 'eSewa', 'BOOK_1569937308', 'paid', '2025-09-04 09:27:31'),
(412, 13, 110, 'Dipesh Rai', 'dipesh.rai412@example.com', 'France', '9817947204', '2026-10-14', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1520089001', 'paid', '2026-03-29 04:45:31'),
(413, 29, 209, 'Bindu Acharya', 'bindu.acharya413@example.com', 'Australia', '9843432002', '2027-02-14', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1747201429', 'paid', '2025-09-23 00:25:53'),
(414, 30, 105, 'Arjun Chhetri', 'arjun.chhetri414@example.com', 'Germany', '9838575903', '2025-10-27', 4, NULL, 'confirmed', 'eSewa', 'BOOK_1654633916', 'paid', '2026-04-25 11:55:41'),
(415, 10, 106, 'Shyam Poudel', 'shyam.poudel415@example.com', 'Nepal', '9816306604', '2026-12-19', 6, NULL, 'pending', 'eSewa', 'BOOK_1671386990', 'pending', '2025-11-20 04:17:13'),
(416, 16, 221, 'Rohan Khadka', 'rohan.khadka416@example.com', 'Germany', '9841845405', '2027-03-01', 6, NULL, 'confirmed', 'eSewa', 'BOOK_1270123329', 'paid', '2025-06-27 06:33:27'),
(417, 32, 203, 'Arjun Magar', 'arjun.magar417@example.com', 'Germany', '9843133678', '2026-07-18', 4, NULL, 'confirmed', 'eSewa', 'BOOK_1218540283', 'paid', '2025-11-11 09:10:32'),
(418, 30, 207, 'Maya Karki', 'maya.karki418@example.com', 'Germany', '9830080282', '2027-04-03', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1595168724', 'paid', '2025-09-04 03:05:02'),
(419, 27, 234, 'Pratima Gurung', 'pratima.gurung419@example.com', 'Canada', '9830159932', '2025-08-17', 1, NULL, 'confirmed', 'eSewa', 'BOOK_1822468959', 'paid', '2026-07-03 07:07:58'),
(420, 10, 232, 'Sujata Bhandari', 'sujata.bhandari420@example.com', 'Canada', '9819868468', '2025-11-03', 10, NULL, 'confirmed', 'eSewa', 'BOOK_1818915791', 'paid', '2026-04-09 01:57:40'),
(421, 17, 206, 'Sushmita Shrestha', 'sushmita.shrestha421@example.com', 'India', '9838291178', '2025-08-24', 1, NULL, 'confirmed', 'eSewa', 'BOOK_1277028302', 'paid', '2026-01-19 05:05:19'),
(422, 21, 211, 'Sandip Gurung', 'sandip.gurung422@example.com', 'Germany', '9811368487', '2027-03-13', 4, NULL, 'confirmed', 'eSewa', 'BOOK_1885575275', 'paid', '2025-09-01 01:46:36'),
(423, 15, 226, 'Pratima Bhattarai', 'pratima.bhattarai423@example.com', 'Japan', '9814510980', '2026-09-11', 1, NULL, 'confirmed', 'eSewa', 'BOOK_1109568052', 'paid', '2025-09-25 04:45:49'),
(424, 13, 104, 'Nirajan Karki', 'nirajan.karki424@example.com', 'UK', '9811042204', '2025-08-15', 4, NULL, 'confirmed', 'eSewa', 'BOOK_1130295852', 'paid', '2026-07-22 04:09:05'),
(425, 16, 230, 'Sita Adhikari', 'sita.adhikari425@example.com', 'Nepal', '9848184254', '2026-11-15', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1802344785', 'paid', '2025-07-16 06:14:03'),
(426, 16, 109, 'Radha Tamang', 'radha.tamang426@example.com', 'UAE', '9847412448', '2027-01-03', 12, NULL, 'pending', 'eSewa', 'BOOK_1012241697', 'pending', '2025-12-03 09:13:03'),
(427, 10, 200, 'Prabin Bhandari', 'prabin.bhandari427@example.com', 'USA', '9836101904', '2026-06-26', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1064187234', 'paid', '2025-07-18 06:10:53'),
(428, 22, 223, 'Pratima Chhetri', 'pratima.chhetri428@example.com', 'Japan', '9821171630', '2025-12-08', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1414182467', 'paid', '2026-01-22 05:55:51'),
(429, 29, 217, 'Radha Dahal', 'radha.dahal429@example.com', 'Germany', '9829621846', '2025-10-12', 2, NULL, 'pending', 'eSewa', 'BOOK_1016643620', 'pending', '2025-09-11 11:10:38'),
(430, 16, 101, 'Sarita Dahal', 'sarita.dahal430@example.com', 'Canada', '9826516231', '2026-09-11', 4, NULL, 'pending', 'eSewa', 'BOOK_1867026595', 'pending', '2025-08-10 12:53:49'),
(431, 29, 218, 'Bindu Sharma', 'bindu.sharma431@example.com', 'Germany', '9827652621', '2026-10-17', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1956287860', 'paid', '2026-05-07 01:33:53'),
(432, 16, 217, 'Bibek Acharya', 'bibek.acharya432@example.com', 'Germany', '9845873538', '2027-02-14', 1, NULL, 'confirmed', 'eSewa', 'BOOK_1805504091', 'paid', '2025-10-16 16:06:24'),
(433, 19, 219, 'Namrata Gurung', 'namrata.gurung433@example.com', 'Nepal', '9836541281', '2026-03-10', 5, NULL, 'confirmed', 'eSewa', 'BOOK_1094168212', 'paid', '2026-03-18 01:05:24'),
(434, 32, 222, 'Bhim Tamang', 'bhim.tamang434@example.com', 'Australia', '9836721846', '2027-01-26', 8, NULL, 'confirmed', 'eSewa', 'BOOK_1216755867', 'paid', '2026-03-16 16:35:30'),
(435, 18, 213, 'Manoj Tamang', 'manoj.tamang435@example.com', 'UK', '9829447906', '2027-03-25', 3, NULL, 'confirmed', 'eSewa', 'BOOK_1159997218', 'paid', '2025-10-11 11:40:49'),
(436, 19, 202, 'Sristi Khadka', 'sristi.khadka436@example.com', 'USA', '9834942239', '2026-11-28', 10, NULL, 'confirmed', 'eSewa', 'BOOK_1301236206', 'paid', '2026-06-15 04:35:38'),
(437, 31, 101, 'Sita Rai', 'sita.rai437@example.com', 'India', '9823733411', '2026-12-21', 8, NULL, 'pending', 'eSewa', 'BOOK_1457360319', 'pending', '2025-09-29 06:31:59'),
(438, 15, 228, 'Bhim Dahal', 'bhim.dahal438@example.com', 'South Korea', '9818784806', '2025-09-18', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1054758156', 'paid', '2026-02-03 06:26:24'),
(439, 10, 11, 'Sabnam Chhetri', 'sabnam.chhetri439@example.com', 'Japan', '9814307438', '2026-09-21', 8, NULL, 'confirmed', 'eSewa', 'BOOK_1687874420', 'paid', '2026-05-26 02:31:20'),
(440, 13, 105, 'Alina Bhattarai', 'alina.bhattarai440@example.com', 'UK', '9840087818', '2026-08-25', 2, NULL, 'pending', 'eSewa', 'BOOK_1377968850', 'failed', '2026-03-28 07:26:02'),
(441, 10, 11, 'Sita Gurung', 'sita.gurung441@example.com', 'UAE', '9844449280', '2026-12-19', 12, NULL, 'pending', 'eSewa', 'BOOK_1213622745', 'pending', '2026-06-28 03:24:20'),
(442, 21, 100, 'Roshan Chhetri', 'roshan.chhetri442@example.com', 'Nepal', '9817074612', '2026-07-08', 5, NULL, 'confirmed', 'eSewa', 'BOOK_1407641857', 'paid', '2026-01-17 08:39:07'),
(443, 17, 228, 'Sabina Thapa', 'sabina.thapa443@example.com', 'Nepal', '9810846515', '2026-02-26', 5, NULL, 'confirmed', 'eSewa', 'BOOK_1664274235', 'paid', '2026-07-08 06:14:53'),
(444, 26, 110, 'Deepak Chhetri', 'deepak.chhetri444@example.com', 'USA', '9835842223', '2025-10-26', 1, NULL, 'confirmed', 'eSewa', 'BOOK_1124135709', 'paid', '2025-12-07 10:35:52'),
(445, 26, 209, 'Maya Basnet', 'maya.basnet445@example.com', 'Nepal', '9813806844', '2026-11-16', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1449106497', 'paid', '2025-10-16 04:43:55'),
(446, 28, 215, 'Kritika Sharma', 'kritika.sharma446@example.com', 'UAE', '9848318513', '2026-07-19', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1969769496', 'paid', '2026-05-02 08:46:06'),
(447, 30, 207, 'Kritika Regmi', 'kritika.regmi447@example.com', 'India', '9824170610', '2026-12-12', 6, NULL, 'confirmed', 'eSewa', 'BOOK_1391142620', 'paid', '2026-03-02 03:31:48'),
(448, 28, 216, 'Sabina Basnet', 'sabina.basnet448@example.com', 'USA', '9836182206', '2026-10-10', 2, NULL, 'pending', 'eSewa', 'BOOK_1154995883', 'failed', '2026-05-04 02:08:46'),
(449, 5, 228, 'Alina Poudel', 'alina.poudel449@example.com', 'France', '9819404866', '2025-08-12', 5, NULL, 'confirmed', 'eSewa', 'BOOK_1979232550', 'paid', '2025-10-30 09:26:23'),
(450, 28, 213, 'Nisha Dahal', 'nisha.dahal450@example.com', 'UK', '9819266022', '2027-01-27', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1085111977', 'paid', '2025-06-27 08:00:11'),
(451, 13, 101, 'Gita Acharya', 'gita.acharya451@example.com', 'Nepal', '9828380342', '2026-03-09', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1070539785', 'paid', '2026-05-17 06:52:19'),
(452, 31, 226, 'Gita Gurung', 'gita.gurung452@example.com', 'France', '9833329816', '2026-05-26', 3, NULL, 'confirmed', 'eSewa', 'BOOK_1819244255', 'paid', '2025-09-02 15:20:00'),
(453, 30, 208, 'Sushmita Adhikari', 'sushmita.adhikari453@example.com', 'Australia', '9822485749', '2026-08-21', 8, NULL, 'confirmed', 'eSewa', 'BOOK_1921349028', 'paid', '2026-07-08 05:59:23'),
(454, 5, 222, 'Rabin Chhetri', 'rabin.chhetri454@example.com', 'France', '9814787666', '2026-08-11', 1, NULL, 'pending', 'eSewa', 'BOOK_1344648337', 'pending', '2025-07-26 08:07:53'),
(455, 27, 21, 'Radha Gurung', 'radha.gurung455@example.com', 'UAE', '9817226856', '2026-12-30', 12, NULL, 'pending', 'eSewa', 'BOOK_1144280441', 'pending', '2025-12-10 16:16:33'),
(456, 5, 215, 'Anjali Basnet', 'anjali.basnet456@example.com', 'South Korea', '9822240242', '2025-11-24', 2, NULL, 'pending', 'eSewa', 'BOOK_1020885531', 'failed', '2026-02-17 08:50:52'),
(457, 15, 107, 'Shyam Karki', 'shyam.karki457@example.com', 'UAE', '9811186977', '2027-03-24', 8, NULL, 'confirmed', 'eSewa', 'BOOK_1376561007', 'paid', '2025-12-01 16:30:44'),
(458, 15, 108, 'Rina Gurung', 'rina.gurung458@example.com', 'UAE', '9818257689', '2026-12-29', 5, NULL, 'confirmed', 'eSewa', 'BOOK_1130505856', 'paid', '2025-09-30 17:03:17'),
(459, 27, 208, 'Sujata Dahal', 'sujata.dahal459@example.com', 'Australia', '9825235939', '2027-03-20', 2, NULL, 'confirmed', 'eSewa', 'BOOK_1681816911', 'paid', '2025-12-02 12:25:52');

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
(12, 21, '6e2817528c66a0ae65f5203a0b774b7494919bec5e6a0877101f2d69a22bbdcc', '2026-07-21 23:17:09');

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
(100, 104, 29, 1, '2026-03-06 13:41:35'),
(101, 116, 16, 7, '2026-05-26 08:28:21'),
(102, 120, 13, 8, '2025-12-23 11:20:34'),
(103, 123, 18, 1, '2026-05-20 12:58:38'),
(104, 119, 10, 2, '2026-06-09 06:45:13'),
(105, 127, 22, 5, '2025-11-25 00:28:59'),
(106, 106, 15, 8, '2025-12-05 07:59:38'),
(107, 122, 18, 7, '2025-12-05 07:50:20'),
(108, 124, 21, 7, '2026-03-30 11:34:16'),
(109, 111, 31, 8, '2026-03-30 04:06:46'),
(110, 115, 22, 4, '2026-04-24 10:41:02'),
(111, 118, 19, 3, '2026-07-23 08:50:59'),
(112, 118, 28, 5, '2026-06-18 06:36:14'),
(113, 112, 19, 8, '2026-03-09 10:31:48'),
(114, 126, 30, 8, '2026-04-01 06:01:50'),
(115, 111, 17, 3, '2026-01-24 15:26:58'),
(116, 128, 32, 1, '2026-03-15 02:08:54'),
(117, 102, 10, 1, '2026-04-13 05:08:40'),
(118, 107, 13, 3, '2026-07-25 06:47:29'),
(119, 111, 10, 8, '2026-02-09 15:16:00'),
(120, 117, 32, 7, '2026-07-24 00:48:46'),
(121, 108, 32, 5, '2026-07-23 17:07:44'),
(122, 121, 28, 3, '2026-06-30 03:48:09'),
(123, 107, 18, 5, '2025-12-31 11:32:50'),
(124, 112, 13, 6, '2025-11-24 12:44:36'),
(200, 200, 20, 1, '2026-04-27 10:00:34'),
(201, 217, 22, 5, '2026-03-26 09:08:28'),
(202, 205, 31, 8, '2026-06-12 06:23:27'),
(203, 218, 26, 1, '2025-12-13 12:38:02'),
(204, 218, 28, 7, '2026-03-18 11:30:24'),
(205, 208, 18, 6, '2026-06-25 06:36:55'),
(206, 204, 13, 8, '2026-01-14 12:48:26'),
(207, 231, 5, 2, '2025-12-03 14:59:53'),
(208, 227, 28, 8, '2026-04-28 02:43:25'),
(209, 231, 16, 9, '2026-07-23 08:02:12'),
(210, 225, 32, 1, '2026-02-27 11:04:24'),
(211, 229, 15, 2, '2026-04-05 02:51:52'),
(212, 200, 15, 8, '2026-06-12 06:51:29'),
(213, 203, 18, 6, '2025-11-22 01:50:44'),
(214, 226, 16, 7, '2026-07-02 04:35:21'),
(215, 212, 31, 1, '2026-04-23 08:48:16'),
(216, 205, 22, 7, '2026-03-19 09:50:25'),
(217, 232, 28, 1, '2026-02-20 09:30:55'),
(218, 224, 28, 9, '2026-03-18 09:27:08'),
(219, 234, 26, 8, '2025-11-19 04:38:59'),
(220, 221, 18, 8, '2026-07-01 10:15:34'),
(221, 204, 28, 6, '2026-07-09 08:29:50'),
(222, 228, 21, 4, '2026-04-11 14:40:59'),
(223, 228, 18, 4, '2026-06-28 05:42:54'),
(224, 207, 10, 3, '2026-06-21 15:26:00'),
(225, 210, 30, 4, '2026-02-27 06:49:53'),
(226, 210, 16, 4, '2025-11-05 03:44:06'),
(227, 212, 13, 1, '2025-12-27 07:57:53'),
(228, 216, 29, 7, '2026-05-09 02:14:44');

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
(8, 'Raghav Pandey', 'Visa', 'smooth process', 5, 1, '2026-06-13 06:19:55'),
(100, 'Nisha Adhikari', 'Camping', 'Best travel agency we have worked with in Nepal.', 4, 1, '2026-01-24 02:21:49'),
(101, 'Sunita Poudel', 'Camping', 'Quick response and very helpful with all our questions.', 5, 1, '2025-08-30 01:51:41'),
(102, 'Maya Poudel', 'Trekking Package', 'Our family trip was perfectly organized down to the last detail.', 5, 1, '2025-12-19 03:15:06'),
(103, 'Deepak Rai', 'Bus Ticketing', 'Affordable pricing without compromising on quality.', 5, 1, '2025-12-11 11:40:29'),
(104, 'Yogesh Poudel', 'Bus Ticketing', 'Affordable pricing without compromising on quality.', 5, 1, '2026-02-01 00:45:06'),
(105, 'Kritika Karki', 'Tour Package', 'Wonderful service from start to finish, highly professional team.', 5, 1, '2026-01-30 09:36:29'),
(106, 'Ritu Rai', 'Bus Ticketing', 'Best travel agency we have worked with in Nepal.', 5, 1, '2025-11-26 03:43:46'),
(107, 'Rajesh Basnet', 'Visa Service', 'Wonderful service from start to finish, highly professional team.', 5, 1, '2026-07-05 03:16:54'),
(108, 'Suman Poudel', 'Tour Package', 'Affordable pricing without compromising on quality.', 5, 1, '2026-03-25 16:26:35'),
(109, 'Anjali Shrestha', 'Bus Ticketing', 'Best travel agency we have worked with in Nepal.', 5, 1, '2026-03-31 12:55:11'),
(110, 'Anjali Poudel', 'Camping', 'Best travel agency we have worked with in Nepal.', 5, 1, '2026-07-13 06:43:37'),
(111, 'Puja Karki', 'Tour Package', 'Affordable pricing without compromising on quality.', 5, 1, '2026-04-13 09:03:45'),
(112, 'Ritu Bhattarai', 'Tour Package', 'Everything was handled smoothly, no stress at all.', 4, 1, '2025-10-26 05:38:20'),
(113, 'Rohan Basnet', 'Tour Package', 'Quick response and very helpful with all our questions.', 5, 1, '2025-10-30 10:53:24'),
(114, 'Sarita Karki', 'Bus Ticketing', 'Wonderful service from start to finish, highly professional team.', 5, 1, '2026-01-28 14:54:11'),
(200, 'Bibek Lama', 'Flight Booking', 'Wonderful service even when we had last-minute changes to our plans.', 4, 1, '2025-08-15 12:46:45'),
(201, 'Kiran Shrestha', 'Hotel Booking', 'Very professional team with quick responses to every question we had.', 4, 1, '2025-10-28 12:12:08'),
(202, 'Prisha Shrestha', 'Bus Ticketing', 'Excellent experience that went above and beyond what we expected.', 4, 1, '2026-05-01 10:13:03'),
(203, 'Yogesh Khadka', 'Visa Service', 'Truly outstanding support with quick responses to every question we had.', 5, 1, '2025-11-26 06:54:20'),
(204, 'Rohan Adhikari', 'Camping', 'Smooth and hassle-free process even when we had last-minute changes to our plans.', 5, 1, '2026-01-11 15:32:07'),
(205, 'Sandip Shrestha', 'Camping', 'Smooth and hassle-free process that went above and beyond what we expected.', 5, 1, '2026-03-07 15:57:35'),
(206, 'Nisha Bhattarai', 'Camping', 'Smooth and hassle-free process from the moment we inquired until we returned home.', 4, 1, '2026-01-06 17:06:18'),
(207, 'Dipesh Sharma', 'Tour Package', 'Wonderful service with quick responses to every question we had.', 5, 1, '2025-08-04 09:37:38'),
(208, 'Bibek Rai', 'Adventure Activities', 'Excellent experience from the moment we inquired until we returned home.', 4, 1, '2025-07-28 04:14:19'),
(209, 'Gita Rai', 'Adventure Activities', 'Excellent experience that made our trip completely stress-free.', 5, 1, '2025-07-12 10:40:25'),
(210, 'Rajesh Shrestha', 'Visa Service', 'Smooth and hassle-free process with quick responses to every question we had.', 5, 1, '2025-11-03 13:57:59'),
(211, 'Puja Poudel', 'Flight Booking', 'Very professional team that made our trip completely stress-free.', 4, 1, '2026-06-23 16:15:54'),
(212, 'Pratima Lama', 'Adventure Activities', 'Truly outstanding support that made our trip completely stress-free.', 5, 1, '2025-07-19 09:05:54'),
(213, 'Arjun Gurung', 'Adventure Activities', 'Truly outstanding support from the moment we inquired until we returned home.', 4, 1, '2026-05-21 12:54:57'),
(214, 'Bhim Bhandari', 'Bus Ticketing', 'Very professional team from the moment we inquired until we returned home.', 5, 1, '2026-04-04 09:21:23'),
(215, 'Dipesh Adhikari', 'Tour Package', 'Smooth and hassle-free process that made our trip completely stress-free.', 3, 1, '2025-09-28 01:17:34'),
(216, 'Pratima Basnet', 'Trekking Package', 'Very professional team that went above and beyond what we expected.', 5, 1, '2026-04-12 10:08:51'),
(217, 'Hari Sharma', 'Trekking Package', 'Truly outstanding support that went above and beyond what we expected.', 4, 1, '2026-07-13 16:32:27'),
(218, 'Karuna Bhandari', 'Hotel Booking', 'Excellent experience even when we had last-minute changes to our plans.', 5, 1, '2025-09-15 06:22:25'),
(219, 'Radha Karki', 'Flight Booking', 'Excellent experience with quick responses to every question we had.', 4, 1, '2025-11-05 00:43:49');

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(280) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
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

INSERT INTO `tours` (`id`, `title`, `slug`, `duration`, `price`, `price_usd`, `old_price`, `overview`, `highlights`, `itinerary`, `includes`, `excludes`, `pdf_file`, `banner_image`, `status`, `is_popular`, `created_at`, `type`, `latitude`, `longitude`, `location_name`) VALUES
(5, 'Everest Base Camp Trek', NULL, '14 Days', 50000.00, 550.00, 60000.00, 'Everest Base Camp Trek is one of the finest treks in the world that centers on the world\'s highest peak Mt. Everest (29,029 ft/ 8,848.68m). This trek will provide you with a natural thrill as it takes you through breathtaking high-altitude landscapes, esoteric Buddhist monasteries, traditional Sherpa villages, high-altitude flora and fauna, and snow-capped mountains.', 'The magnificent views of the world’s highest peak, Mt. Everest (8,848.68m)\r\nWorld’s highest airport at Syangboche (3,780m)\r\nExplore wide range of flora and fauna at Sagarmatha National Park\r\nWildlife like musk deer, colorful pheasants, snow leopards, and Himalayan Tahrs\r\nChance to explore the culture and lifestyles of the local Sherpa people\r\nPrayer wheels, colorful flags, Mani stones, high suspension bridges\r\nVisit an ancient monastery in Tengboche\r\nHighest glacier on Earth- Khumbu Glacier (4,900 m)\r\nAmazing panoramic views from Kala Patthar (5,555m)\r\nViews of other peaks such as Mt. Lhotse(8,516m), Cho Oyu (8,201m) and Mt. Makalu (8,463m)', 'Day\r\n1\r\nFlight from Kathmandu/Manthali to Lukla. Flight time: Approx 40 min from KTM/20 min from Manthali. Trek to Phakding (2,650 m). Trek time: Approx. 3 hrs.\r\nDay\r\n2\r\nTrek from Phakding to Namche Bazaar (3,440 m). Trek time: Approx. 6 hrs.\r\nDay\r\n3\r\nRest day and acclimatization at Namche Bazaar.\r\nDay\r\n4\r\nTrek from Namche to Tengboche/ Deboche (3,855 m). Trek time: Approx. 5 hrs.\r\nDay\r\n5\r\nTrek from Tengboche to Dingboche (4,360 m). Trek time: Approx. 5 hrs.\r\nDay\r\n6\r\nRest day and acclimatization at Dingboche.\r\nDay\r\n7\r\nTrek from Dingboche to Lobuche (4,930 m). Trek time: Approx. 5 hrs.\r\nDay\r\n8\r\nTrek from Lobuche to EBC (5,364 m) and back to Gorak Shep (5,185 m). Trek time: Approx. 7 hrs.\r\nDay\r\n9\r\nHike to Kala Patthar (5,555 m) viewpoint, trek to Gorak Shep, then to Pheriche (4,250 m). Trek time: Approx. 5 hrs.\r\nDay\r\n10\r\nTrek from Pheriche to Tengboche (3,855 m). Trek time: Approx. 5 hrs.\r\nDay\r\n11\r\nTrek from Tengboche to Namche Bazaar (3,440 m). Trek time: Approx. 5 hrs.\r\nDay\r\n12\r\nTrek from Namche Bazaar to Phakding (2,650 m). Trek time: Approx. 4 hrs.\r\nDay\r\n13\r\nTrek from Phakding to Lukla (2,850 m). Trek time: Approx. 4 hrs.\r\nDay\r\n14\r\nFly back to Kathmandu/ Manthali from Lukla. Flight time: Approx. 40 min for KTM/20 min for Manthali. Drive time: Approx. 5 hrs from Manthali to KTM', 'Transportation\r\nAccommodations\r\nFood\r\nGuide and Porter\r\nTrek permit and expenses\r\nMedical Assistance\r\nSouvenir\r\nFarewell', 'International Flight\r\nAccommodations\r\nFood\r\nGuide and Porter\r\nVisa\r\nTravel Insurance\r\nPersonal Expenses', '1766770952_Important-Questions.pdf', '1766770952_Everest-base-Camp-trek.jpeg', 1, 1, '2025-12-26 17:42:32', 'domestic', 28.0022, 86.8523, NULL),
(10, 'Manaslu Circuit Trek', NULL, '7 Days', 25000.00, 300.00, NULL, 'The Manaslu Circuit Trek is a rewarding Himalayan journey — a blend of rugged landscapes, cultural depth, and high-altitude adventure that leaves trekkers with memories of both breathtaking scenery and genuine human connection.', 'Scenic drive from Kathmandu to Soti Khola\r\nViews of the world\'s highest peaks- including Manaslu mountain (8,156m), Lamjung Himal, Mt.Annapurna II, etc.\r\nTrek along the Budhi Gandaki River gorge\r\nThe highest point on the trek - Larkya La Pass (5,106m / 16,751ft)\r\nRich biodiversity and beautiful natural scenery\r\nCaptivating flora and fauna\r\nInsight into Hindu and Buddhist culture\r\nPossibility of spotting wild endangered species like snow leopard', NULL, 'Transportation\r\nAccommodations\r\nFood\r\nGuide and Porter\r\nTrek Permits and Expenses\r\nMedical Assistance\r\nSouvenir\r\nFarewell', 'International Flight\r\nAccommodations\r\nFood\r\nGuide and Porter\r\nVisa\r\nTravel Insurance\r\nPersonal Expenses', '1768076727_Log-sheet-Sample.pdf', '1768076727_manaslu_circuit_trek.webp', 1, 0, '2026-01-10 20:25:27', 'domestic', NULL, NULL, NULL),
(13, 'Bali', NULL, '4 Nights / 5 Days', 115000.00, 800.00, NULL, 'This 4-night, 5-day Bali itinerary offers a blend of coastal adventure and cultural discovery. Based in the Kuta area, the trip includes water sports like banana boat rides at Tanjung Benoa and a sunset visit to the Uluwatu Temple. You will explore the island\'s interior with a visit to the Kintamani volcano and the artistic heritage of Ubud. A major highlight is a full-day fast boat excursion to Nusa Penida to see iconic landmarks such as Kelingking Beach and Angel\'s Billabong.', 'This 4-night Bali getaway combines Kuta\'s vibrant beaches and water sports with a scenic tour of the Kintamani volcano, the artistic charm of Ubud, and a stunning day trip to the iconic cliffs and shores of Nusa Penida.', NULL, 'International Ticket: KTM-DPS-KTM\r\n4 Nights hotel accommodation in Bali\r\nDaily breakfast at hotel\r\nAirport Transfer (Pick up and drop off)\r\nWatersports at Tanjung Benoa (Banana Boat)\r\nUluwatu Sunset Temple Tour\r\nKintamani Volcano viewpoint tour\r\nUbud Art Village exploration\r\nNusa Penida Island full-day tour with fast boat transfers\r\nLunch in Nusa Penida Island\r\nVisa fees\r\nAll tours and transfers on SIC Basis', 'Meals aside from those specifically included.\r\nCity and Resort Taxes (If Applicable)\r\nSurcharge (If Applicable)\r\nPersonal Expenses\r\nTips\r\nAny Other charge which is not mentioned in above inclusions', '1771841158_Important-Questions (1).pdf', '1771841158_bali-for-digital-nomads.jpg', 1, 1, '2026-02-23 10:05:58', 'international', NULL, NULL, NULL),
(15, 'Annapurna Base Camp Trek', NULL, '6 Days', 25500.00, 400.00, 30000.00, 'The Annapurna Base Camp (ABC) Trek is one of Nepal’s most renowned Himalayan adventures, set within the breathtaking Annapurna Sanctuary, a natural glacial basin enclosed by towering snow-covered peaks. The trek showcases an extraordinary variety of landscapes, from lush forests to high alpine terrain, combined with rich mountain culture and dramatic scenery. Surrounded by iconic summits including Annapurna I, Annapurna South, Hiunchuli, and the sacred Machhapuchhre, this trek offers a truly immersive and unforgettable Himalayan experience.', 'Close-up Himalayan views including Annapurna I & Machhapuchhre\r\nWalk inside the spectacular Annapurna Sanctuary\r\nSunrise and sunset at Annapurna Base Camp (4,130m)\r\nDramatic 360° mountain amphitheatre of snow peaks\r\nTraditional Gurung village experience in Chhomrong\r\nDense rhododendron, bamboo & alpine forests\r\nWaterfalls, rivers & suspension bridges along Modi Khola\r\nMachhapuchhre Base Camp panoramic viewpoint\r\nGlacier valley landscapes and high alpine terrain\r\nNatural hot spring experience at Jhinu\r\nDiverse landscapes from jungle to glacial basin\r\nPerfect mix of adventure + culture + nature', NULL, 'Pokhara – Jhinu Danda – Pokhara transportation (jeep)\r\nExperienced licensed trekking guide\r\nTeahouse/lodge accommodation during trek\r\n3 meals per day during trekking (Breakfast, Lunch, Dinner)', 'Personal trekking gear (jacket, sleeping bag, gloves, poles, etc.)\r\nSnacks, soft drinks, chocolates & bottled water\r\nPersonal expenses (extra food, drinks, shopping)\r\nAnything not mentioned in “Package Includes”', '1782919225_testing_pdf.pdf', '1782919225_day4abc.jpg', 1, 1, '2026-07-01 15:20:25', 'domestic', 28.5300, 83.8780, 'Annapurna Base Camp'),
(16, 'Langtang Trek', NULL, '6 Days', 20500.00, 400.00, 25000.00, 'Langtang Valley, located north of Kathmandu inside Langtang National Park, is one of Nepal’s most beautiful Himalayan trekking regions. This trail offers a perfect mix of snow peaks, glaciers, rivers, forests, culture, and mountain villages. The trek leads to Kyanjin Gompa, a high Himalayan settlement surrounded by dramatic peaks, and the famous viewpoint Kyanjin Ri, offering 360° Himalayan panoramas. This trek is perfect for travelers seeking real mountains, cultural experience, glacier views, and peaceful nature, all in just 6 days.', 'Close-up Himalayan views including Langtang Lirung\r\nBeautiful forests, rivers, waterfalls & suspension bridges\r\nVisit traditional Tamang & Tibetan-influenced villages\r\nExplore Kyanjin Gompa Monastery and local cheese factory\r\nSunrise hike to Kyanjin Ri (4,773m)\r\nSnow landscapes in winter & green valleys in spring\r\nLess crowded than Everest/Annapurna\r\nPerfect mix of adventure + culture + nature', NULL, 'Kathmandu – Syabrubesi – Kathmandu transportation (bus/jeep)\r\nExperienced trekking guide\r\nTeahouse/lodge accommodation during trek\r\n3 meals per day during trekking (Breakfast, Lunch, Dinner)', 'Snacks, soft drinks, chocolates & bottled water\r\nPersonal expenses (extra food, drinks, shopping)\r\nAnything not mentioned in “Package Includes”', '1782920446_testing_pdf.pdf', '1782920446_111887_65ec4e8d1cf2a.jpg', 1, 1, '2026-07-01 15:40:46', 'domestic', 28.2106, 85.5714, 'Langtang'),
(17, 'Mardi Himal Trek', NULL, '5 Days', 16500.00, 300.00, 20000.00, 'The Mardi Himal Trek is one of Nepal\'s \"hidden gems,\" offering a quiet, ridge-top trail with spectacular views of Machhapuchhre (Fishtail) and the Annapurna massif. To fit this into 5 days from Kathmandu, you will need to utilize a flight or a very early private drive to Pokhara on Day 1 to maximize your trekking time.', 'The Ridge Trail: Offers constant 360-degree mountain views because you walk along a high ridge rather than deep in a valley.\r\nFishtail Proximity: Provides the closest possible view of the sacred Mt. Machhapuchhre (Fishtail), which towers directly over High Camp.\r\nBadal Danda: A spectacular viewpoint where you often stand above a \"sea of clouds\" covering the lower valleys.\r\nRhododendron Forests: Features ancient, moss-covered forests that bloom with vibrant red and pink flowers during the spring.\r\nEfficient Altitude: The fastest trek in the Annapurna region to reach 4,500m, making it perfect for a short 5-day trip.', NULL, 'Kathmandu – Pokhara – Kathmandu transportation (Bus)\r\nExperienced licensed trekking guide\r\nTeahouse/lodge accommodation during trek\r\n3 meals per day during trekking (Breakfast, Lunch, Dinner)\r\nTour coordination & support throughout the trip', 'Snacks, soft drinks, chocolates & bottled water\r\nPersonal expenses (extra food, drinks, shopping)\r\nAnything not mentioned in “Package Includes”', '1782921754_testing_pdf.pdf', '1782921754_mardi-himal-trek.jpg', 1, 1, '2026-07-01 16:02:34', 'domestic', 28.7195, 83.9448, 'Mardi Himal High Camp'),
(18, 'Thailand Tour', NULL, '4 Nights 5 Days', 70000.00, 500.00, 90000.00, 'This Thailand holiday blends the lively beach atmosphere of Pattaya with the cultural charm and modern energy of Bangkok. Enjoy a refreshing island escape to Coral Island (Koh Larn), relax by the sea, and explore Thailand’s famous temples, city sights, and shopping spots. With comfortable hotel stays, guided tours, and smooth transfers, this trip offers the perfect balance of sightseeing, leisure, and tropical fun — ideal for couples, families, and holiday travellers looking for a complete Thailand experience.', 'Speedboat tour to Coral Island (Koh Larn)\r\nBangkok Half-Day City Tour with Golden Buddha & Marble Temple\r\nPrivate airport transfers\r\n4 nights hotel stay with daily breakfast\r\nCoral Island lunch included\r\nFree shopping & leisure day in Bangkok\r\nPattaya beach stay + Bangkok city experience', NULL, 'International Flight ( KTM DMK KTM 7 KG HANDCARRY)\r\nAirport Pick Up & Drop on PVT\r\n2 Nights hotel accommodation in Pattaya. ( 4 Star)\r\n2 Nights hotel accommodation in Bangkok. ( 3 Star)\r\nEvery Day Breakfast\r\nPattaya to Bangkok Transfer on PVT\r\nCoral Island tour with Lunch & Transfer\r\nHalf Day City Tour 2 Temples on SIC\r\nVisa fee\r\nAll Tours & Transfers on SIC Basis', 'Meals aside from those specifically included.\r\nCity and Resort Taxes If Applicable\r\nSurcharge If Applicable\r\nPersonal Expenses\r\nTips\r\nAny Other charge which is not mentioned in above inclusions.', '1782923675_testing_pdf.pdf', '1782923675_4600_t8afNwa2.jpg', 1, 1, '2026-07-01 16:34:35', 'international', 15.8700, 100.9925, 'Thailand'),
(19, 'Ghorepani Poon Hill Trek', NULL, '5 Days', 10000.00, 270.00, 12000.00, 'A short and popular trek in the Annapurna region, famous for sunrise views over the Himalayas. The trek passes through beautiful villages, forests, and traditional Gurung communities.', 'Poon Hill sunrise viewpoint\r\nAnnapurna and Dhaulagiri views\r\nGurung culture\r\nRhododendron forests', NULL, 'Guide\r\nAccommodation\r\nTransportation\r\nTrekking permit', 'Personal expenses\r\nInsurance', '1782924293_testing_pdf.pdf', '1782924293_Poon hill.jpg0.60816600 1731412954.webp', 1, 1, '2026-07-01 16:44:53', 'domestic', 28.4000, 81.6900, 'Poon Hill'),
(20, 'Dubai City Tour', NULL, '5 Days', 180000.00, 1350.00, 250000.00, 'Experience Dubai’s modern architecture, desert adventures, shopping, and famous attractions.', 'Burj Khalifa\r\nDesert safari\r\nDubai Mall\r\nMarina cruise', NULL, 'Hotel accommodation\r\nAirport transfers\r\nCity sightseeing\r\nDesert safari\r\nTour guide\r\nTransportation', 'Flight tickets\r\nVisa charges\r\nPersonal expenses\r\nTravel insurance\r\nExtra activities', '1782927319_testing_pdf.pdf', '1782927319_7c70ab1d-5b73-4916-a4a6-3cfdd2e876b7.webp', 1, 1, '2026-07-01 17:35:19', 'international', 25.2048, 55.2708, 'Dubai'),
(21, 'Switzerland Mountain Tour', NULL, '7 Days', 300000.00, 2300.00, 320000.00, 'Switzerland Mountain Tour is a scenic European adventure featuring breathtaking Alpine landscapes, beautiful lakes, charming villages, and world-famous mountain destinations. Experience Swiss culture, mountain railways, and unforgettable views of the Swiss Alps.', 'Swiss Alps mountain views\r\nInterlaken adventure town\r\nJungfrau mountain region\r\nBeautiful Swiss villages\r\nLake Lucerne sightseeing\r\nScenic train journeys\r\nSwiss chocolate and local culture', NULL, 'Hotel accommodation\r\nAirport transfers\r\nTransportation\r\nSightseeing tours\r\nProfessional guide\r\nMountain excursion tickets', 'Flight tickets\r\nVisa fees\r\nTravel insurance\r\nPersonal expenses\r\nExtra activities', '1782927567_testing_pdf.pdf', '1782927567_scl_swiss_alps_switzerland_001_3000x1500_fa1f809c9f21_a5322cf159.webp', 1, 1, '2026-07-01 17:39:27', 'international', 46.8182, 8.2275, 'Switzerland'),
(22, 'Rara Lake Tour', 'rara-lake-tour', '6 Days', 45000.00, 340.00, 60000.00, 'Rara Lake Tour takes you to the largest lake of Nepal, located in the remote and peaceful Mugu district. Surrounded by forests, mountains, and beautiful landscapes, Rara offers a perfect escape for nature lovers. The journey provides scenic views, local culture, boating experiences, and a peaceful Himalayan environment away from crowded cities.', 'Visit Nepal’s largest lake\r\nBeautiful Himalayan landscapes\r\nBoating on Rara Lake\r\nExplore Rara National Park\r\nForest walks and nature photography\r\nExperience remote western Nepal culture\r\nPeaceful mountain environment', NULL, 'Transportation\r\nHotel/lodge accommodation\r\nGuide\r\nSightseeing\r\nBoating at Rara Lake\r\nRequired permits', 'Flight tickets (if not included)\r\nPersonal expenses\r\nTravel insurance\r\nExtra activities\r\nMeals outside package', '1782927845_testing_pdf.pdf', '1782927845_5da2ff_a56e03ed850a41c1b2b4f30671b87789~mv2.webp', 1, 1, '2026-07-01 17:44:05', 'domestic', 29.5300, 82.0800, 'Rara Lake'),
(23, 'Api Himal Base Camp Trek', NULL, '14 Days', 40000.00, 400.00, 45000.00, 'The Api Himal Base Camp Trek is one of Nepal\'s most remote and least-explored trekking adventures. Located in the Api Nampa Conservation Area of Darchula District, this trek offers spectacular mountain scenery, untouched forests, alpine meadows, glacial rivers, and authentic Himalayan culture.\r\n\r\nThe trail passes through traditional villages inhabited by Byansi, Chhetri, and other local communities before reaching the base camp of Api Himal (7,132 m), the highest mountain in Nepal\'s Sudurpashchim Province.\r\n\r\nUnlike the crowded Everest and Annapurna regions, the Api Himal trek provides a peaceful wilderness experience with breathtaking views of Api Himal, Nampa Himal, and surrounding peaks.', 'Trek to the base of Api Himal (7,132 m)\r\nExplore Api Nampa Conservation Area\r\nVisit remote Himalayan villages\r\nStunning alpine landscapes and waterfalls\r\nRich biodiversity and wildlife\r\nAuthentic local culture and hospitality\r\nLess crowded trekking route\r\nPanoramic mountain views', NULL, 'Airport transfers\r\nDomestic flights (Kathmandu–Dhangadhi–Kathmandu)\r\nPrivate jeep transportation\r\nLicensed trekking guide\r\nPorter service\r\nAccommodation during the trek\r\nThree meals during trekking\r\nApi Nampa Conservation Area entry permit\r\nGovernment taxes', 'Nepal visa\r\nInternational airfare\r\nTravel insurance\r\nPersonal expenses\r\nAlcoholic and soft drinks\r\nTips for guides and porters\r\nEmergency evacuation', '1784482219_testing_pdf.pdf', '1784482219_api.webp', 0, 1, '2026-07-19 17:30:19', 'domestic', 30.1027, 80.9216, 'Api Base Camp Darchula'),
(26, 'Kathmandu Valley Heritage Tour', NULL, '3 Days', 8000.00, 65.00, 10000.00, 'Explore the UNESCO World Heritage Sites of Kathmandu Valley including Durbar Squares, ancient temples, and vibrant local culture.', 'Kathmandu Durbar Square\r\nPatan Durbar Square\r\nBhaktapur Durbar Square\r\nSwayambhunath Stupa\r\nPashupatinath Temple', NULL, 'Hotel accommodation\r\nGuide\r\nEntrance fees\r\nTransportation', 'Personal expenses\r\nMeals not mentioned', NULL, 'default-tour.jpg', 1, 0, '2026-01-25 09:54:51', 'domestic', 27.7172, 85.3240, 'Kathmandu'),
(27, 'Nagarkot Sunrise Tour', NULL, '2 Days', 6000.00, 48.00, NULL, 'A short escape from Kathmandu to Nagarkot, famous for sunrise views over the Himalayan range including Everest on clear days.', 'Himalayan sunrise views\r\nHiking trails\r\nLocal village walk\r\nPeaceful hillside resort stay', NULL, 'Hotel stay\r\nBreakfast\r\nTransportation\r\nGuide', 'Lunch and dinner\r\nPersonal expenses', NULL, 'default-tour.jpg', 1, 0, '2026-02-04 10:14:42', 'domestic', 27.7172, 85.5220, 'Nagarkot'),
(28, 'Chitwan Jungle Safari', NULL, '3 Days', 18000.00, 140.00, 22000.00, 'Experience Nepal wildlife at Chitwan National Park with jungle safaris, canoe rides, and Tharu cultural programs.', 'Jeep safari in Chitwan National Park\r\nCanoe ride on Rapti River\r\nElephant breeding center visit\r\nTharu cultural dance show', NULL, 'Resort accommodation\r\nAll meals\r\nSafari activities\r\nNaturalist guide', 'Alcoholic beverages\r\nPersonal expenses\r\nTips', NULL, 'default-tour.jpg', 1, 0, '2026-06-20 08:22:56', 'domestic', 27.5291, 84.3542, 'Chitwan'),
(29, 'Pokhara Lake City Tour', NULL, '3 Days', 12000.00, 95.00, 15000.00, 'Discover Pokhara stunning lakes, caves, and mountain views on this relaxed getaway perfect for families and couples.', 'Phewa Lake boating\r\nDavis Falls\r\nGupteshwor Cave\r\nSarangkot sunrise viewpoint\r\nWorld Peace Pagoda', NULL, 'Hotel accommodation\r\nBreakfast\r\nSightseeing tours\r\nTransportation', 'Lunch and dinner\r\nPersonal expenses\r\nParagliding (optional)', NULL, 'default-tour.jpg', 1, 0, '2026-01-15 04:32:18', 'domestic', 28.2096, 83.9856, 'Pokhara'),
(30, 'Bandipur Heritage Village Tour', NULL, '2 Days', 7000.00, 55.00, NULL, 'A charming Newari hill town with preserved heritage architecture, mountain views, and peaceful countryside walks.', 'Preserved Newari architecture\r\nMountain viewpoints\r\nSiddha Cave exploration\r\nLocal handicraft shopping', NULL, 'Hotel stay\r\nBreakfast\r\nGuide\r\nTransportation', 'Lunch and dinner\r\nPersonal expenses', NULL, 'default-tour.jpg', 1, 1, '2026-01-22 10:28:43', 'domestic', 27.9333, 84.4167, 'Bandipur'),
(31, 'Singapore City Explorer', NULL, '4 Nights 5 Days', 95000.00, 720.00, 120000.00, 'A vibrant city break through Singapore futuristic gardens, iconic skyline, and diverse cultural neighborhoods.', 'Gardens by the Bay\r\nMarina Bay Sands SkyPark\r\nSentosa Island\r\nUniversal Studios Singapore\r\nChinatown and Little India', NULL, 'International flight\r\nHotel accommodation\r\nDaily breakfast\r\nCity tour\r\nAirport transfers', 'Personal expenses\r\nOptional activities\r\nVisa fee', NULL, 'default-tour.jpg', 1, 0, '2026-03-17 15:31:57', 'international', 1.3521, 103.8198, 'Singapore'),
(32, 'Kuala Lumpur City Tour', NULL, '3 Nights 4 Days', 75000.00, 570.00, NULL, 'Explore Malaysia dynamic capital city, home to the iconic Petronas Towers and rich multicultural heritage.', 'Petronas Twin Towers\r\nBatu Caves\r\nKL Tower\r\nCentral Market\r\nGenting Highlands day trip', NULL, 'Hotel accommodation\r\nDaily breakfast\r\nCity tour\r\nAirport transfers', 'International flight\r\nPersonal expenses\r\nVisa fee', NULL, 'default-tour.jpg', 1, 0, '2026-07-01 14:08:17', 'international', 3.1390, 101.6869, 'Kuala Lumpur');

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
(200, 22, 6, 'Nepalgunj to Kathmandu', 'Return to Kathmandu by flight or road. Complete the Rara Lake adventure with unforgettable memories of Nepal’s natural beauty.'),
(215, 23, 1, 'Arrival in Kathmandu', 'Arrival in Kathmandu'),
(216, 23, 2, 'Fly to Dhangadhi', 'Fly to Dhangadhi'),
(217, 23, 3, 'Drive to Gokuleshwor', 'Drive to Gokuleshwor'),
(218, 23, 4, 'Drive to Latinath', 'Drive to Latinath'),
(219, 23, 5, 'Trek to Khandeshwori', 'Trek to Khandeshwori'),
(220, 23, 6, 'Trek to Ghusa', 'Trek to Ghusa'),
(221, 23, 7, 'Trek to Simar', 'Trek to Simar'),
(222, 23, 8, 'Trek to Api Himal Base Camp', 'Trek to Api Himal Base Camp'),
(223, 23, 9, 'Explore Base Camp and return to Simar', 'Explore Base Camp and return to Simar'),
(224, 23, 10, 'Trek to Ghusa', 'Trek to Ghusa'),
(225, 23, 11, 'Trek to Latinath', 'Trek to Latinath'),
(226, 23, 12, 'Drive to Dhangadhi', 'Drive to Dhangadhi'),
(227, 23, 13, 'Fly to Kathmandu', 'Fly to Kathmandu'),
(228, 23, 14, 'Departure', 'Departure'),
(300, 26, 1, 'Kathmandu and Patan Durbar Square', 'Morning visit to Kathmandu Durbar Square followed by Patan Durbar Square in the afternoon. Explore ancient palaces, temples and courtyards.'),
(301, 26, 2, 'Bhaktapur and Swayambhunath', 'Full day exploring Bhaktapur pottery square and Swayambhunath Stupa (Monkey Temple) with panoramic valley views.'),
(302, 26, 3, 'Pashupatinath and Departure', 'Visit the sacred Pashupatinath Temple in the morning before departure.'),
(303, 27, 1, 'Drive to Nagarkot', 'Scenic drive from Kathmandu to Nagarkot. Check in and enjoy sunset views from the hotel.'),
(304, 27, 2, 'Sunrise and Return', 'Early morning hike to the sunrise viewpoint, breakfast, then return drive to Kathmandu.'),
(305, 28, 1, 'Arrival and Jungle Walk', 'Arrive at Chitwan, check in to resort, evening jungle walk and Tharu cultural show.'),
(306, 28, 2, 'Jeep Safari and Canoeing', 'Full day jeep safari inside Chitwan National Park followed by a canoe ride on the Rapti River.'),
(307, 28, 3, 'Elephant Center and Departure', 'Visit the elephant breeding center before departure back to Kathmandu.'),
(308, 29, 1, 'Arrival and Phewa Lake', 'Arrive in Pokhara, check in, evening boating on Phewa Lake.'),
(309, 29, 2, 'Sarangkot Sunrise and Caves', 'Early morning drive to Sarangkot for sunrise, then visit Davis Falls and Gupteshwor Cave.'),
(310, 29, 3, 'Leisure and Departure', 'Free morning for shopping or optional paragliding before departure.'),
(311, 30, 1, 'Arrival in Bandipur', 'Drive from Kathmandu to Bandipur, explore the heritage town square in the evening.'),
(312, 30, 2, 'Siddha Cave and Viewpoint', 'Morning visit to Siddha Cave, afternoon at the mountain viewpoint before departure.'),
(313, 31, 1, 'Arrival and Gardens by the Bay', 'Arrive in Singapore, evening visit to Gardens by the Bay light show.'),
(314, 31, 2, 'Sentosa Island', 'Full day at Sentosa Island including Universal Studios Singapore.'),
(315, 31, 3, 'City Tour', 'Marina Bay Sands SkyPark, Chinatown and Little India walking tour.'),
(316, 31, 4, 'Leisure Day', 'Free day for shopping at Orchard Road.'),
(317, 31, 5, 'Departure', 'Transfer to airport for departure.'),
(318, 32, 1, 'Arrival and Petronas Towers', 'Arrive in Kuala Lumpur, evening visit to Petronas Twin Towers light show.'),
(319, 32, 2, 'Batu Caves and City Tour', 'Morning visit to Batu Caves, afternoon KL Tower and Central Market.'),
(320, 32, 3, 'Genting Highlands', 'Full day trip to Genting Highlands theme park.'),
(321, 32, 4, 'Departure', 'Free morning before airport transfer.');

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
(15, 5, NULL, 'Bipin Chapai', 4, 'dammi package maja aayo!', 1, '2026-06-13 06:13:18'),
(500, 5, 116, 'Anjali Khadka', 4, 'Very good service, guide was friendly and accommodation was clean.', 1, '2025-12-12 13:50:00'),
(501, 16, 123, 'Arjun Adhikari', 3, 'Good value for money, enjoyed every moment.', 1, '2026-05-12 13:23:02'),
(502, 10, 113, 'Maya Rai', 1, 'Bus was delayed and communication was poor during the trip.', 1, '2025-08-19 03:37:49'),
(503, 28, 115, 'Anisha Tamang', 5, 'The team took great care of us throughout the journey.', 1, '2026-04-27 13:16:11'),
(504, 22, 115, 'Deepak Tamang', 4, 'Smooth booking process and the trip went as planned.', 0, '2025-08-01 03:39:55'),
(505, 30, 129, 'Rina Thapa', 3, 'Decent experience but the itinerary felt rushed on some days.', 1, '2026-04-01 07:16:42'),
(506, 22, 117, 'Rina Poudel', 4, 'Highly recommend this package to anyone visiting Nepal.', 1, '2025-11-08 12:58:53'),
(507, 5, 111, 'Arjun Thapa', 4, 'Great trip overall, food could have been a bit better but the views made up for it.', 1, '2026-07-07 03:53:27'),
(508, 22, NULL, 'Prisha Karki', 3, 'Trip was okay but communication before the trip could improve.', 1, '2025-10-04 06:31:02'),
(509, 5, 112, 'Rina Karki', 5, 'Good value for money, enjoyed every moment.', 1, '2026-06-21 10:54:20'),
(510, 15, NULL, 'Kritika Poudel', 5, 'Stunning views and excellent hospitality, thank you DTP.', 1, '2025-12-29 10:40:44'),
(511, 16, 124, 'Maya Khadka', 3, 'Decent experience but the itinerary felt rushed on some days.', 1, '2026-04-28 09:40:35'),
(512, 21, 127, 'Ritu Magar', 5, 'Highly recommend this package to anyone visiting Nepal.', 1, '2025-09-19 10:44:28'),
(513, 18, 110, 'Maya Gurung', 4, 'Good value for money, enjoyed every moment.', 1, '2026-03-03 16:57:40'),
(514, 13, 119, 'Sunita Bhattarai', 5, 'One of the best travel experiences I have had, highly recommended.', 1, '2026-04-16 04:16:02'),
(515, 30, 129, 'Puja Chhetri', 4, 'The team took great care of us throughout the journey.', 1, '2025-09-07 07:00:44'),
(516, 27, 100, 'Dipesh Bhattarai', 3, 'Decent experience but the itinerary felt rushed on some days.', 1, '2025-06-27 04:04:27'),
(517, 31, 115, 'Karuna Lama', 4, 'Everything exceeded my expectations, will book again.', 1, '2026-05-25 14:23:51'),
(518, 31, 120, 'Yogesh Chhetri', 5, 'Good value for money, enjoyed every moment.', 1, '2025-12-12 16:42:53'),
(519, 29, 11, 'Bishal Thapa', 1, 'Bus was delayed and communication was poor during the trip.', 1, '2025-07-07 08:08:40'),
(520, 31, 117, 'Bishal Gurung', 5, 'Everything exceeded my expectations, will book again.', 1, '2025-07-26 09:30:17'),
(521, 32, 109, 'Sunita Karki', 4, 'Great trip overall, food could have been a bit better but the views made up for it.', 0, '2025-08-05 05:00:13'),
(522, 28, 129, 'Puja Sharma', 5, 'Very good service, guide was friendly and accommodation was clean.', 1, '2026-04-12 13:39:57'),
(523, 5, 124, 'Sabina Sharma', 5, 'One of the best travel experiences I have had, highly recommended.', 1, '2026-01-27 10:03:24'),
(524, 28, 114, 'Sabina Rai', 5, 'Good value for money, enjoyed every moment.', 1, '2026-03-09 13:46:01'),
(525, 27, 129, 'Karuna Tamang', 5, 'Stunning views and excellent hospitality, thank you DTP.', 1, '2025-09-11 01:13:25'),
(526, 5, 108, 'Anisha Basnet', 4, 'Great trip overall, food could have been a bit better but the views made up for it.', 1, '2026-04-24 01:31:24'),
(527, 29, 124, 'Deepak Bhattarai', 4, 'Very good service, guide was friendly and accommodation was clean.', 1, '2025-12-23 09:08:05'),
(528, 32, 114, 'Kiran Gurung', 5, 'Absolutely incredible experience, the guide was very knowledgeable and supportive throughout.', 1, '2025-06-22 02:03:01'),
(529, 18, 109, 'Sunita Tamang', 4, 'One of the best travel experiences I have had, highly recommended.', 1, '2025-11-26 03:51:13'),
(530, 20, 110, 'Sarita Magar', 4, 'Smooth booking process and the trip went as planned.', 1, '2025-07-09 04:04:52'),
(531, 21, 101, 'Karuna Thapa', 5, 'Great trip overall, food could have been a bit better but the views made up for it.', 1, '2025-10-05 12:40:45'),
(532, 19, 107, 'Ritu Magar', 4, 'Great trip overall, food could have been a bit better but the views made up for it.', 1, '2026-07-05 11:49:27'),
(533, 13, 100, 'Anisha Karki', 4, 'Good value for money, enjoyed every moment.', 1, '2025-11-18 03:42:23'),
(534, 29, 127, 'Anjali Khadka', 5, 'Stunning views and excellent hospitality, thank you DTP.', 1, '2025-11-01 08:54:51'),
(535, 30, 117, 'Suman Lama', 5, 'Everything exceeded my expectations, will book again.', 1, '2026-03-23 02:32:56'),
(536, 29, 124, 'Suman Sharma', 5, 'The team took great care of us throughout the journey.', 1, '2025-11-15 10:26:31'),
(537, 20, 117, 'Nisha Sharma', 4, 'Very good service, guide was friendly and accommodation was clean.', 1, '2025-11-04 06:20:15'),
(538, 30, 11, 'Kiran Khadka', 4, 'Good value for money, enjoyed every moment.', 0, '2025-11-17 15:05:01'),
(539, 19, 119, 'Maya Magar', 2, 'Trip was okay but communication before the trip could improve.', 1, '2026-01-19 15:50:33'),
(540, 32, 129, 'Deepak Thapa', 4, 'Very good service, guide was friendly and accommodation was clean.', 1, '2026-03-20 07:22:46'),
(541, 15, 111, 'Rohan Rai', 5, 'Stunning views and excellent hospitality, thank you DTP.', 1, '2025-07-13 15:32:46'),
(542, 31, 106, 'Sandip Rai', 5, 'The team took great care of us throughout the journey.', 1, '2026-02-25 07:38:11'),
(543, 32, 102, 'Sita Adhikari', 5, 'Best trip of my life. Well organized from start to finish.', 1, '2026-02-27 04:55:55'),
(544, 15, 118, 'Sabina Basnet', 4, 'One of the best travel experiences I have had, highly recommended.', 1, '2025-12-13 10:26:03'),
(545, 30, 125, 'Sabina Gurung', 3, 'Great trip overall, food could have been a bit better but the views made up for it.', 1, '2025-10-04 01:24:09'),
(546, 21, 107, 'Nisha Bhattarai', 4, 'Great trip overall, food could have been a bit better but the views made up for it.', 1, '2025-12-25 08:04:33'),
(547, 29, 127, 'Kritika Magar', 5, 'Highly recommend this package to anyone visiting Nepal.', 0, '2025-09-12 01:54:47'),
(548, 18, 116, 'Maya Gurung', 3, 'Average experience, weather affected some views but not their fault.', 0, '2026-05-07 07:26:35'),
(549, 5, 11, 'Kritika Sharma', 2, 'Trip was okay but communication before the trip could improve.', 1, '2026-03-30 10:00:18'),
(550, 29, 116, 'Ritu Bhattarai', 4, 'Great trip overall, food could have been a bit better but the views made up for it.', 1, '2025-09-09 06:42:07'),
(551, 16, 109, 'Bikash Sharma', 1, 'Bus was delayed and communication was poor during the trip.', 1, '2026-05-03 09:53:47'),
(552, 21, 129, 'Rajesh Thapa', 4, 'Everything exceeded my expectations, will book again.', 1, '2025-08-02 12:32:32'),
(553, 29, 102, 'Dipesh Karki', 5, 'Great trip overall, food could have been a bit better but the views made up for it.', 0, '2025-07-15 10:53:16'),
(554, 19, 101, 'Yogesh Poudel', 4, 'Average experience, weather affected some views but not their fault.', 1, '2026-03-11 02:03:48'),
(555, 31, 117, 'Anjali Magar', 3, 'Average experience, weather affected some views but not their fault.', 1, '2025-12-15 15:20:30'),
(556, 22, 110, 'Suman Karki', 3, 'Very good service, guide was friendly and accommodation was clean.', 1, '2025-08-05 15:33:42'),
(557, 32, 105, 'Suman Thapa', 5, 'Absolutely incredible experience, the guide was very knowledgeable and supportive throughout.', 1, '2026-02-11 04:04:25'),
(558, 5, 129, 'Puja Sharma', 4, 'Average experience, weather affected some views but not their fault.', 1, '2026-04-21 16:38:39'),
(559, 29, 113, 'Deepak Adhikari', 3, 'Smooth booking process and the trip went as planned.', 1, '2026-05-20 09:43:56'),
(700, 16, 231, 'Kritika Magar', 3, 'After reading reviews, the local communities we visited were warm and welcoming. I\'ve already recommended it to my friends.', 1, '2025-07-21 04:17:52'),
(701, 31, NULL, 'Alina Dahal', 2, 'Traveling with my family, small hiccups with scheduling were handled quickly by the team. A few things could be improved, but overall a great experience.', 1, '2025-05-25 01:07:43'),
(702, 19, 223, 'Rohan Bhattarai', 5, 'Right from booking to the trip itself, the guide was extremely knowledgeable about the local culture and terrain. Would absolutely book with them again.', 1, '2025-05-25 14:50:03'),
(703, 32, 204, 'Shyam Regmi', 5, 'I wasn\'t sure what to expect, but the transportation was punctual and comfortable throughout. This trip is now one of my favorite travel memories.', 0, '2025-10-25 02:57:33'),
(704, 30, NULL, 'Puja Basnet', 5, 'Having traveled a lot before, the accommodation exceeded what I expected for the price. This trip is now one of my favorite travel memories.', 1, '2025-07-13 14:46:54'),
(705, 30, 212, 'Nabin Bhandari', 4, 'Having traveled a lot before, the guide was extremely knowledgeable about the local culture and terrain. A solid, well-organized trip overall.', 1, '2026-05-12 10:31:41'),
(706, 21, 203, 'Sristi Adhikari', 5, 'After reading reviews, the porter and support staff worked incredibly hard for us. A few things could be improved, but overall a great experience.', 1, '2025-08-16 03:59:13'),
(707, 21, NULL, 'Sunita Neupane', 4, 'Booking last minute, the safety measures gave me real peace of mind at high altitude. Can\'t wait to book another package with them.', 1, '2026-04-15 09:20:59'),
(708, 21, 228, 'Nisha Bhattarai', 1, 'As a solo traveler, the accommodation exceeded what I expected for the price. Exceeded my expectations in almost every way.', 1, '2026-04-10 06:19:37'),
(709, 31, 103, 'Alina Adhikari', 4, 'Having traveled a lot before, the views along the way were beyond anything photos can capture. A few things could be improved, but overall a great experience.', 1, '2026-05-30 11:29:31'),
(710, 30, 231, 'Kamal Chhetri', 5, 'Traveling with my family, the guide was extremely knowledgeable about the local culture and terrain. A few things could be improved, but overall a great experience.', 1, '2025-12-31 10:01:09'),
(711, 27, NULL, 'Maya Bhattarai', 5, 'As a first-time trekker, the accommodation exceeded what I expected for the price. I\'ve already recommended it to my friends.', 1, '2026-05-26 07:00:00'),
(712, 21, 100, 'Nabin Khadka', 5, 'Having traveled a lot before, the views along the way were beyond anything photos can capture. Highly recommend to anyone considering this trip.', 1, '2025-12-19 09:09:03'),
(713, 10, 227, 'Alina Poudel', 1, 'Having traveled a lot before, the food arrangements were thoughtful, even for dietary restrictions. Worth every rupee spent.', 1, '2026-04-20 12:05:27'),
(714, 27, 203, 'Gita Lama', 3, 'Booking last minute, small hiccups with scheduling were handled quickly by the team. Worth every rupee spent.', 1, '2025-12-08 04:56:55'),
(715, 10, 221, 'Arjun Magar', 4, 'Booking last minute, the food arrangements were thoughtful, even for dietary restrictions. A few things could be improved, but overall a great experience.', 1, '2026-03-18 08:40:41'),
(716, 30, 210, 'Nabin Karki', 5, 'Booking last minute, the local communities we visited were warm and welcoming. Highly recommend to anyone considering this trip.', 1, '2025-11-12 15:50:14'),
(717, 22, 215, 'Anjali Shrestha', 4, 'As a solo traveler, the local communities we visited were warm and welcoming. A few things could be improved, but overall a great experience.', 1, '2026-02-01 02:35:15'),
(718, 18, 233, 'Puja Bhattarai', 4, 'From the very first day, the local communities we visited were warm and welcoming. Communication could be better next time.', 1, '2026-03-10 11:03:03'),
(719, 26, 213, 'Anjali Adhikari', 4, 'Honestly, small hiccups with scheduling were handled quickly by the team. Exceeded my expectations in almost every way.', 1, '2026-03-21 12:40:41'),
(720, 21, NULL, 'Nirajan Dahal', 4, 'From the very first day, the food arrangements were thoughtful, even for dietary restrictions. Would absolutely book with them again.', 1, '2025-11-18 00:19:25'),
(721, 31, 214, 'Kritika Thapa', 3, 'As a solo traveler, the transportation was punctual and comfortable throughout. Worth every rupee spent.', 1, '2025-11-01 04:07:46'),
(722, 29, 208, 'Bishal Dahal', 5, 'Right from booking to the trip itself, small hiccups with scheduling were handled quickly by the team. Would absolutely book with them again.', 1, '2026-07-07 09:23:40'),
(723, 28, 100, 'Manoj Bhattarai', 5, 'Right from booking to the trip itself, the accommodation exceeded what I expected for the price. Highly recommend to anyone considering this trip.', 0, '2026-03-15 08:05:38'),
(724, 32, 104, 'Sabina Acharya', 1, 'Having traveled a lot before, the transportation was punctual and comfortable throughout. This trip is now one of my favorite travel memories.', 0, '2025-10-30 07:50:15'),
(725, 28, 106, 'Prabin Lama', 5, 'Having traveled a lot before, the guide was extremely knowledgeable about the local culture and terrain. Would absolutely book with them again.', 1, '2026-06-15 08:29:42'),
(726, 26, 108, 'Sandip Khadka', 4, 'I wasn\'t sure what to expect, but the transportation was punctual and comfortable throughout. Would absolutely book with them again.', 1, '2025-08-11 12:27:00'),
(727, 31, 219, 'Bhim Karki', 1, 'Right from booking to the trip itself, the pace of the itinerary was just right, not too rushed. Can\'t wait to book another package with them.', 1, '2026-03-30 14:29:16'),
(728, 21, 214, 'Sristi Lama', 4, 'Right from booking to the trip itself, the porter and support staff worked incredibly hard for us. Can\'t wait to book another package with them.', 1, '2025-08-20 01:53:09'),
(729, 10, 203, 'Hari Gurung', 5, 'I wasn\'t sure what to expect, but the guide was extremely knowledgeable about the local culture and terrain. A solid, well-organized trip overall.', 1, '2026-04-23 12:43:57'),
(730, 22, 211, 'Prabin Regmi', 5, 'Right from booking to the trip itself, the accommodation exceeded what I expected for the price. A few things could be improved, but overall a great experience.', 1, '2025-07-09 14:17:19'),
(731, 27, 200, 'Anjali Adhikari', 5, 'As a first-time trekker, the views along the way were beyond anything photos can capture. Can\'t wait to book another package with them.', 1, '2026-06-15 11:41:56'),
(732, 18, 227, 'Anjali Gurung', 3, 'Traveling with my family, the views along the way were beyond anything photos can capture. This trip is now one of my favorite travel memories.', 1, '2025-07-30 15:27:23'),
(733, 29, 230, 'Sita Lama', 3, 'I wasn\'t sure what to expect, but the views along the way were beyond anything photos can capture. I\'ve already recommended it to my friends.', 0, '2026-03-22 12:17:24'),
(734, 13, 101, 'Maya Khadka', 3, 'From the very first day, the safety measures gave me real peace of mind at high altitude. Worth every rupee spent.', 1, '2026-03-09 10:54:02'),
(735, 22, 101, 'Amrita Tamang', 3, 'Having traveled a lot before, the safety measures gave me real peace of mind at high altitude. Would absolutely book with them again.', 1, '2026-07-14 07:21:30'),
(736, 29, 208, 'Sristi Shrestha', 3, 'Traveling with my family, the safety measures gave me real peace of mind at high altitude. Communication could be better next time.', 1, '2026-07-22 10:07:44'),
(737, 19, 225, 'Radha Shrestha', 4, 'As a first-time trekker, the porter and support staff worked incredibly hard for us. Highly recommend to anyone considering this trip.', 0, '2026-03-22 13:19:41'),
(738, 32, 206, 'Nabin Adhikari', 1, 'Booking last minute, the views along the way were beyond anything photos can capture. A few things could be improved, but overall a great experience.', 1, '2025-09-10 02:28:06'),
(739, 29, 102, 'Kamal Basnet', 4, 'Honestly, the pace of the itinerary was just right, not too rushed. A few things could be improved, but overall a great experience.', 1, '2025-07-09 04:04:53'),
(740, 20, 216, 'Manoj Chhetri', 5, 'After reading reviews, the safety measures gave me real peace of mind at high altitude. I\'ve already recommended it to my friends.', 1, '2026-03-22 05:30:15'),
(741, 18, 215, 'Alina Regmi', 5, 'As a first-time trekker, the accommodation exceeded what I expected for the price. Communication could be better next time.', 1, '2026-03-30 03:56:29'),
(742, 15, 223, 'Bikash Magar', 3, 'From the very first day, the transportation was punctual and comfortable throughout. Worth every rupee spent.', 1, '2026-03-29 03:18:12'),
(743, 18, 228, 'Namrata Adhikari', 2, 'Right from booking to the trip itself, the views along the way were beyond anything photos can capture. Exceeded my expectations in almost every way.', 1, '2025-06-25 00:21:40'),
(744, 26, 202, 'Puja Adhikari', 5, 'I wasn\'t sure what to expect, but the guide was extremely knowledgeable about the local culture and terrain. I\'ve already recommended it to my friends.', 1, '2026-07-07 07:07:00'),
(745, 28, 213, 'Bikash Acharya', 5, 'As a first-time trekker, the food arrangements were thoughtful, even for dietary restrictions. A solid, well-organized trip overall.', 1, '2025-10-19 15:19:26'),
(746, 27, 210, 'Ritu Adhikari', 5, 'Booking last minute, the food arrangements were thoughtful, even for dietary restrictions. Exceeded my expectations in almost every way.', 1, '2025-12-29 09:57:19'),
(747, 10, 201, 'Bhim Khadka', 4, 'Having traveled a lot before, the porter and support staff worked incredibly hard for us. I\'ve already recommended it to my friends.', 1, '2025-08-31 06:40:46'),
(748, 5, 205, 'Ritu Dahal', 5, 'Traveling with my family, the food arrangements were thoughtful, even for dietary restrictions. Communication could be better next time.', 1, '2026-01-21 15:04:10'),
(749, 10, 102, 'Sabnam Regmi', 5, 'Booking last minute, the food arrangements were thoughtful, even for dietary restrictions. Communication could be better next time.', 1, '2026-04-30 04:37:18'),
(750, 17, 212, 'Sarita Thapa', 4, 'Right from booking to the trip itself, the accommodation exceeded what I expected for the price. Communication could be better next time.', 1, '2025-05-24 02:13:30'),
(751, 27, 214, 'Sagar Bhattarai', 4, 'Right from booking to the trip itself, the porter and support staff worked incredibly hard for us. A few things could be improved, but overall a great experience.', 1, '2025-09-15 07:08:30'),
(752, 18, 222, 'Sunita Thapa', 5, 'From the very first day, the local communities we visited were warm and welcoming. Exceeded my expectations in almost every way.', 1, '2026-03-22 06:17:56'),
(753, 10, NULL, 'Sarita Lama', 3, 'After reading reviews, the transportation was punctual and comfortable throughout. Exceeded my expectations in almost every way.', 1, '2026-02-19 07:42:24'),
(754, 29, 102, 'Sristi Chhetri', 5, 'Booking last minute, the transportation was punctual and comfortable throughout. A few things could be improved, but overall a great experience.', 1, '2026-03-28 15:03:39'),
(755, 29, 208, 'Rajesh Lama', 5, 'Honestly, the transportation was punctual and comfortable throughout. Communication could be better next time.', 1, '2026-01-20 03:06:28'),
(756, 10, 220, 'Bhim Regmi', 2, 'From the very first day, the food arrangements were thoughtful, even for dietary restrictions. Highly recommend to anyone considering this trip.', 1, '2026-06-16 02:03:32'),
(757, 16, 207, 'Manoj Thapa', 3, 'From the very first day, the accommodation exceeded what I expected for the price. A solid, well-organized trip overall.', 1, '2025-11-17 10:06:58'),
(758, 19, 210, 'Kiran Bhandari', 3, 'Right from booking to the trip itself, the views along the way were beyond anything photos can capture. A solid, well-organized trip overall.', 1, '2026-03-08 14:24:16'),
(759, 30, 215, 'Kiran Khadka', 4, 'I wasn\'t sure what to expect, but the porter and support staff worked incredibly hard for us. This trip is now one of my favorite travel memories.', 1, '2026-07-08 06:26:25'),
(760, 20, 216, 'Sunita Regmi', 3, 'As a first-time trekker, the local communities we visited were warm and welcoming. A few things could be improved, but overall a great experience.', 1, '2026-07-02 12:10:28'),
(761, 15, 223, 'Prakash Bhattarai', 3, 'Having traveled a lot before, small hiccups with scheduling were handled quickly by the team. Communication could be better next time.', 1, '2026-01-19 04:38:21'),
(762, 29, 203, 'Arjun Regmi', 3, 'I wasn\'t sure what to expect, but the food arrangements were thoughtful, even for dietary restrictions. A solid, well-organized trip overall.', 1, '2026-03-19 09:55:55'),
(763, 22, 102, 'Amrita Lama', 5, 'From the very first day, the guide was extremely knowledgeable about the local culture and terrain. Worth every rupee spent.', 0, '2025-12-25 16:38:57'),
(764, 30, 200, 'Pratima Khadka', 5, 'I wasn\'t sure what to expect, but the porter and support staff worked incredibly hard for us. Would absolutely book with them again.', 1, '2026-02-21 03:48:22'),
(765, 28, 223, 'Sagar Acharya', 5, 'After reading reviews, the safety measures gave me real peace of mind at high altitude. A solid, well-organized trip overall.', 1, '2026-05-06 04:15:59'),
(766, 16, 105, 'Nisha Bhattarai', 4, 'As a solo traveler, the accommodation exceeded what I expected for the price. Highly recommend to anyone considering this trip.', 1, '2025-06-06 08:15:03'),
(767, 32, 233, 'Gita Acharya', 4, 'As a first-time trekker, the porter and support staff worked incredibly hard for us. A solid, well-organized trip overall.', 0, '2026-03-21 06:12:00'),
(768, 32, 203, 'Bhim Rai', 5, 'From the very first day, the local communities we visited were warm and welcoming. A few things could be improved, but overall a great experience.', 1, '2026-07-20 06:24:26'),
(769, 31, 204, 'Sarita Gurung', 4, 'Traveling with my family, the porter and support staff worked incredibly hard for us. A few things could be improved, but overall a great experience.', 0, '2025-07-21 16:00:34'),
(770, 28, 206, 'Prakash Basnet', 5, 'As a solo traveler, the accommodation exceeded what I expected for the price. Can\'t wait to book another package with them.', 1, '2025-08-31 01:22:21'),
(771, 20, 106, 'Rabin Adhikari', 4, 'From the very first day, the safety measures gave me real peace of mind at high altitude. Exceeded my expectations in almost every way.', 1, '2026-02-25 06:20:56'),
(772, 17, 110, 'Kiran Karki', 2, 'Having traveled a lot before, the pace of the itinerary was just right, not too rushed. Worth every rupee spent.', 1, '2026-01-08 10:53:15'),
(773, 32, 201, 'Anisha Basnet', 4, 'As a solo traveler, the transportation was punctual and comfortable throughout. Exceeded my expectations in almost every way.', 1, '2025-10-07 10:05:13'),
(774, 13, 207, 'Rohan Bhandari', 5, 'After reading reviews, the food arrangements were thoughtful, even for dietary restrictions. A few things could be improved, but overall a great experience.', 0, '2026-05-05 11:24:44'),
(775, 10, 204, 'Roshan Khadka', 3, 'Honestly, the guide was extremely knowledgeable about the local culture and terrain. Highly recommend to anyone considering this trip.', 1, '2026-04-15 03:11:55'),
(776, 27, 202, 'Bikash Tamang', 5, 'Right from booking to the trip itself, the pace of the itinerary was just right, not too rushed. Worth every rupee spent.', 1, '2025-05-30 09:45:06'),
(777, 18, 201, 'Rajesh Adhikari', 4, 'Having traveled a lot before, the views along the way were beyond anything photos can capture. I\'ve already recommended it to my friends.', 1, '2026-03-04 02:00:48'),
(778, 22, 102, 'Shyam Sharma', 2, 'After reading reviews, small hiccups with scheduling were handled quickly by the team. Can\'t wait to book another package with them.', 1, '2025-06-18 13:16:27'),
(779, 15, 213, 'Hari Tamang', 4, 'As a first-time trekker, the transportation was punctual and comfortable throughout. Would absolutely book with them again.', 0, '2025-10-05 09:25:27'),
(780, 18, 211, 'Sristi Dahal', 4, 'As a first-time trekker, the transportation was punctual and comfortable throughout. Highly recommend to anyone considering this trip.', 1, '2026-01-30 16:31:36'),
(781, 21, 103, 'Bhim Tamang', 5, 'I wasn\'t sure what to expect, but the pace of the itinerary was just right, not too rushed. Can\'t wait to book another package with them.', 1, '2025-11-17 03:55:20'),
(782, 27, 201, 'Sabnam Karki', 2, 'Traveling with my family, the accommodation exceeded what I expected for the price. Communication could be better next time.', 1, '2026-02-21 08:42:57'),
(783, 17, 101, 'Radha Bhandari', 5, 'Traveling with my family, the pace of the itinerary was just right, not too rushed. Can\'t wait to book another package with them.', 1, '2025-08-30 01:37:37'),
(784, 16, 228, 'Bindu Adhikari', 5, 'As a solo traveler, small hiccups with scheduling were handled quickly by the team. I\'ve already recommended it to my friends.', 1, '2025-10-03 07:23:21'),
(785, 19, 108, 'Sagar Thapa', 5, 'Booking last minute, the pace of the itinerary was just right, not too rushed. This trip is now one of my favorite travel memories.', 1, '2025-07-21 04:30:46'),
(786, 31, 212, 'Prakash Rai', 5, 'As a first-time trekker, the food arrangements were thoughtful, even for dietary restrictions. Worth every rupee spent.', 1, '2026-05-03 03:27:24'),
(787, 16, 206, 'Amrita Rai', 5, 'Having traveled a lot before, the safety measures gave me real peace of mind at high altitude. Communication could be better next time.', 1, '2026-03-05 07:11:24'),
(788, 5, 103, 'Arjun Chhetri', 4, 'Traveling with my family, the local communities we visited were warm and welcoming. Worth every rupee spent.', 1, '2026-07-15 04:31:38'),
(789, 5, 104, 'Sandip Basnet', 4, 'I wasn\'t sure what to expect, but the local communities we visited were warm and welcoming. A solid, well-organized trip overall.', 1, '2025-08-19 07:58:11');

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
(11, 'Bipin Chapai', 'bipinchapai2059@gmail.com', '$2y$10$1.KsgzFuLGKMCoySsPxbMuotf.2HwU6xFt4nhFjS/XuzndjhEdEG.', '9745355605', NULL, NULL, 1, '2026-05-21 04:08:35', '2026-07-27 12:26:12', NULL),
(21, 'Kushal', 'acharyakushal629@gmail.com', '$2y$10$NCG1UIvPQYxYa7XBHrk2EeYraKW2.qZly1rGQFZXftKtVUPGASpn2', '9745355605', 'Khairahani-13, Chitwan', 'Nepal', 1, '2026-06-15 11:18:47', '2026-07-27 14:13:11', '2026-07-01 12:57:09'),
(100, 'Kiran Gurung', 'kiran.gurung100@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9811678443', NULL, 'Australia', 1, '2026-03-19 07:23:47', '2026-03-22 08:37:27', NULL),
(101, 'Sita Sharma', 'sita.sharma101@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9816287781', NULL, 'UK', 1, '2026-03-25 16:53:01', '2026-04-11 19:14:28', NULL),
(102, 'Arjun Thapa', 'arjun.thapa102@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9810436124', NULL, 'USA', 1, '2025-07-30 13:36:17', '2025-08-03 16:06:05', NULL),
(103, 'Manoj Gurung', 'manoj.gurung103@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9834090698', NULL, 'UAE', 1, '2025-09-16 09:06:02', '2025-10-09 09:59:24', NULL),
(104, 'Bikash Adhikari', 'bikash.adhikari104@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9829674861', NULL, 'UAE', 1, '2025-09-30 07:00:04', '2025-10-01 15:05:54', NULL),
(105, 'Sunita Lama', 'sunita.lama105@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9816778091', NULL, 'Germany', 1, '2026-03-02 14:55:53', '2026-03-13 17:13:42', NULL),
(106, 'Deepak Khadka', 'deepak.khadka106@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9814791741', NULL, 'USA', 1, '2025-10-22 07:25:29', '2025-11-03 13:43:20', NULL),
(107, 'Sandip Bhattarai', 'sandip.bhattarai107@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9813753932', NULL, 'UK', 1, '2026-07-06 10:40:17', '2026-07-08 16:13:41', NULL),
(108, 'Sabina Karki', 'sabina.karki108@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9840793026', NULL, 'USA', 1, '2026-03-09 04:30:47', '2026-03-26 19:57:37', NULL),
(109, 'Manoj Shrestha', 'manoj.shrestha109@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9824718366', NULL, 'USA', 1, '2025-11-04 15:20:48', '2025-11-05 10:40:10', NULL),
(110, 'Ritu Poudel', 'ritu.poudel110@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9838330675', NULL, 'India', 1, '2026-01-06 12:53:29', '2026-01-22 06:43:46', NULL),
(111, 'Prisha Poudel', 'prisha.poudel111@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9846035468', NULL, 'Australia', 1, '2025-06-24 10:22:18', '2025-07-07 06:46:56', NULL),
(112, 'Sabnam Thapa', 'sabnam.thapa112@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9843593765', NULL, 'USA', 1, '2025-11-05 04:10:40', '2025-11-14 22:38:12', NULL),
(113, 'Nabin Shrestha', 'nabin.shrestha113@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9820841372', NULL, 'Nepal', 1, '2025-09-19 10:46:01', '2025-09-22 15:15:03', NULL),
(114, 'Sunita Chhetri', 'sunita.chhetri114@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9848074679', NULL, 'India', 1, '2026-06-09 16:07:04', '2026-07-03 10:08:42', NULL),
(115, 'Sabina Adhikari', 'sabina.adhikari115@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9821081482', NULL, 'Australia', 1, '2025-10-25 13:28:59', '2025-11-11 12:45:19', NULL),
(116, 'Manoj Poudel', 'manoj.poudel116@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9835059825', NULL, 'Canada', 1, '2025-10-31 14:22:15', NULL, NULL),
(117, 'Aarav Magar', 'aarav.magar117@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9847173044', NULL, 'UK', 1, '2025-09-24 07:15:04', '2025-10-16 13:04:57', NULL),
(118, 'Sita Lama', 'sita.lama118@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9832174680', NULL, 'India', 1, '2025-11-01 07:32:42', '2025-11-16 10:46:59', NULL),
(119, 'Dipesh Magar', 'dipesh.magar119@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9848668909', NULL, 'Canada', 1, '2026-03-20 16:06:26', NULL, NULL),
(120, 'Maya Karki', 'maya.karki120@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9833776507', NULL, 'Germany', 1, '2025-12-24 15:10:46', '2025-12-25 09:03:25', NULL),
(121, 'Sabnam Shrestha', 'sabnam.shrestha121@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9817332920', NULL, 'UK', 1, '2026-04-15 06:49:28', '2026-04-19 14:29:15', NULL),
(122, 'Anisha Chhetri', 'anisha.chhetri122@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9815058994', NULL, 'Canada', 1, '2025-06-04 03:18:41', '2025-06-21 08:59:48', NULL),
(123, 'Anisha Rai', 'anisha.rai123@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9821160949', NULL, 'Germany', 1, '2025-11-16 15:28:55', '2025-11-28 11:24:00', NULL),
(124, 'Manoj Thapa', 'manoj.thapa124@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9840535094', NULL, 'Australia', 1, '2025-12-18 15:24:12', '2025-12-27 07:37:47', NULL),
(125, 'Nisha Sharma', 'nisha.sharma125@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9831045662', NULL, 'Nepal', 1, '2026-06-27 15:47:58', '2026-07-24 07:32:05', NULL),
(126, 'Anisha Tamang', 'anisha.tamang126@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9814598388', NULL, 'India', 1, '2025-08-11 07:40:07', '2025-09-10 13:37:38', NULL),
(127, 'Sita Magar', 'sita.magar127@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9815501813', NULL, 'Germany', 1, '2025-08-20 16:35:59', '2025-08-28 16:15:16', NULL),
(128, 'Manoj Tamang', 'manoj.tamang128@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9830132463', NULL, 'Canada', 1, '2026-02-11 02:15:29', '2026-03-02 09:04:34', NULL),
(129, 'Rohan Adhikari', 'rohan.adhikari129@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9827797298', NULL, 'USA', 1, '2026-01-25 03:11:15', '2026-02-05 20:53:34', NULL),
(200, 'Kiran Thapa', 'kiran.thapa200@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9836496156', NULL, 'USA', 1, '2025-05-28 03:38:37', '2025-05-31 12:02:05', NULL),
(201, 'Nabin Basnet', 'nabin.basnet201@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9816087647', NULL, 'India', 1, '2025-05-25 03:29:40', '2025-07-04 07:36:37', NULL),
(202, 'Bishal Gurung', 'bishal.gurung202@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9847357148', NULL, 'UAE', 1, '2025-12-20 04:49:07', '2026-01-25 11:06:37', NULL),
(203, 'Manoj Khadka', 'manoj.khadka203@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9816538455', NULL, 'USA', 1, '2025-10-07 01:54:13', '2025-11-07 19:49:20', NULL),
(204, 'Karuna Khadka', 'karuna.khadka204@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9830117022', 'Bharatpur-10, Chitwan', 'Nepal', 1, '2025-07-30 07:20:36', '2025-08-18 16:46:28', '2025-08-20 09:32:26'),
(205, 'Suman Poudel', 'suman.poudel205@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9820199509', NULL, 'Canada', 1, '2026-07-02 03:03:35', '2026-07-20 16:21:44', NULL),
(206, 'Sristi Dahal', 'sristi.dahal206@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9840615421', 'Lakeside, Pokhara', 'Nepal', 1, '2026-03-06 15:59:42', NULL, NULL),
(207, 'Prabin Dahal', 'prabin.dahal207@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9839906445', 'Bhaktapur Durbar Area, Bhaktapur', 'Nepal', 1, '2025-08-14 11:16:29', '2025-09-05 09:31:03', '2025-09-14 10:47:15'),
(208, 'Ritu Bhattarai', 'ritu.bhattarai208@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9843320000', 'Patan, Lalitpur', 'Nepal', 1, '2025-12-29 09:11:08', '2026-01-25 14:45:26', NULL),
(209, 'Kamal Bhattarai', 'kamal.bhattarai209@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9825485471', 'Bharatpur-10, Chitwan', 'Nepal', 1, '2026-05-06 07:57:14', '2026-05-06 11:16:18', '2026-05-19 17:39:36'),
(210, 'Kiran Thapa', 'kiran.thapa210@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9844594044', NULL, 'India', 1, '2025-12-01 12:40:25', '2025-12-26 18:03:12', '2026-01-01 20:10:07'),
(211, 'Maya Bhandari', 'maya.bhandari211@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9813528289', 'Dhangadhi-3, Kailali', 'Nepal', 1, '2026-05-06 03:38:39', NULL, '2026-05-19 10:40:16'),
(212, 'Rajesh Bhandari', 'rajesh.bhandari212@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9834438594', NULL, 'USA', 1, '2025-05-14 15:44:30', '2025-06-13 10:06:47', '2025-06-21 21:53:44'),
(213, 'Suman Regmi', 'suman.regmi213@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9811549927', 'Adarsh Nagar, Birgunj', 'Nepal', 1, '2026-01-18 04:59:34', '2026-01-19 15:41:55', '2026-01-27 22:23:58'),
(214, 'Suman Khadka', 'suman.khadka214@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9824951368', NULL, 'France', 1, '2026-02-04 07:54:51', '2026-02-16 18:47:51', '2026-03-04 21:22:46'),
(215, 'Sita Sharma', 'sita.sharma215@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9828751460', NULL, 'Australia', 1, '2025-08-02 11:43:51', '2025-08-24 17:05:14', '2025-09-08 12:21:13'),
(216, 'Nirajan Bhandari', 'nirajan.bhandari216@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9810128064', NULL, 'Germany', 1, '2025-06-08 03:08:42', '2025-06-15 12:30:56', '2025-07-05 16:05:51'),
(217, 'Gita Bhattarai', 'gita.bhattarai217@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9841082177', NULL, 'USA', 1, '2025-07-16 05:25:08', '2025-07-17 20:51:41', '2025-08-05 21:42:59'),
(218, 'Rajesh Thapa', 'rajesh.thapa218@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9846819952', NULL, 'India', 1, '2026-07-15 03:48:47', '2026-07-17 12:52:55', '2026-07-25 12:18:32'),
(219, 'Sabina Dahal', 'sabina.dahal219@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9831876772', 'Bhaktapur Durbar Area, Bhaktapur', 'Nepal', 1, '2025-05-21 04:18:58', '2025-06-12 22:26:52', NULL),
(220, 'Alina Thapa', 'alina.thapa220@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9845690169', 'Adarsh Nagar, Birgunj', 'Nepal', 1, '2026-07-13 15:04:11', NULL, NULL),
(221, 'Rina Thapa', 'rina.thapa221@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9841775572', NULL, 'USA', 1, '2025-10-11 01:35:43', '2025-11-13 21:50:49', '2025-11-30 07:15:12'),
(222, 'Nisha Gurung', 'nisha.gurung222@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9816559574', NULL, 'France', 1, '2026-07-08 02:43:20', '2026-07-27 22:12:44', '2026-07-27 21:32:15'),
(223, 'Bindu Regmi', 'bindu.regmi223@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9827420943', NULL, 'Australia', 1, '2025-05-18 14:23:26', '2025-05-25 16:04:42', '2025-05-27 12:42:19'),
(224, 'Sunita Thapa', 'sunita.thapa224@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9834574144', 'Dharan-8, Sunsari', 'Nepal', 1, '2026-05-13 14:29:47', '2026-05-19 21:10:42', NULL),
(225, 'Suman Lama', 'suman.lama225@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9844601669', NULL, 'Canada', 1, '2026-04-13 11:35:05', NULL, NULL),
(226, 'Dipesh Sharma', 'dipesh.sharma226@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9835792926', 'Dhangadhi-3, Kailali', 'Nepal', 1, '2026-02-21 16:19:07', '2026-03-07 09:05:16', '2026-03-12 14:48:08'),
(227, 'Anisha Adhikari', 'anisha.adhikari227@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9837242697', 'Dharan-8, Sunsari', 'Nepal', 1, '2025-11-01 15:59:20', '2025-11-06 11:27:57', '2025-11-06 08:51:16'),
(228, 'Anjali Bhandari', 'anjali.bhandari228@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9824925547', 'Nepalgunj-6, Banke', 'Nepal', 1, '2026-05-21 14:15:21', '2026-06-25 14:39:08', '2026-07-02 09:10:16'),
(229, 'Prisha Shrestha', 'prisha.shrestha229@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9823540437', NULL, 'UAE', 1, '2025-10-24 06:33:28', '2025-11-25 14:22:51', '2025-12-03 07:00:01'),
(230, 'Gita Regmi', 'gita.regmi230@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9846980280', NULL, 'France', 1, '2025-11-21 08:14:28', '2025-11-27 19:42:31', NULL),
(231, 'Ritu Regmi', 'ritu.regmi231@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9830654970', NULL, 'Australia', 1, '2026-01-28 07:08:56', '2026-03-09 17:03:53', '2026-03-11 14:27:10'),
(232, 'Prisha Tamang', 'prisha.tamang232@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9835560543', NULL, 'UAE', 1, '2025-09-19 07:59:18', '2025-09-21 11:17:28', '2025-10-02 16:35:20'),
(233, 'Sabina Gurung', 'sabina.gurung233@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9830773409', 'Bharatpur-10, Chitwan', 'Nepal', 1, '2026-07-22 10:39:05', '2026-07-25 12:15:32', NULL),
(234, 'Anjali Adhikari', 'anjali.adhikari234@example.com', '$2y$10$abcdefghijklmnopqrstuuVGvV6nq0y0S1E3G2pQeYQ1nQ9r7hLqW', '9816023248', 'Dhangadhi-3, Kailali', 'Nepal', 1, '2026-07-01 12:16:19', '2026-07-10 08:37:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_activity`
--

CREATE TABLE `user_activity` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `action` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `view_count` int(11) DEFAULT NULL,
  `time_spent` decimal(32,0) DEFAULT NULL,
  `last_viewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activity`
--

INSERT INTO `user_activity` (`id`, `user_id`, `package_id`, `action`, `view_count`, `time_spent`, `last_viewed_at`, `created_at`) VALUES
(1, 21, 5, 'view', 24, 4151, '2026-07-19 06:58:57', '2026-06-19 08:42:50'),
(14, 21, 17, 'view', 50, 2903, '2026-07-26 17:35:59', '2026-07-01 16:27:32'),
(19, 21, 18, 'view', 6, 65, '2026-07-01 16:35:49', '2026-07-01 16:35:06'),
(26, 21, 21, 'view', 7, 117, '2026-07-27 08:29:23', '2026-07-01 17:48:51'),
(27, 21, 22, 'view', 148, 14880, '2026-07-26 18:00:35', '2026-07-01 17:49:13'),
(28, 21, 19, 'view', 77, 3011, '2026-07-27 08:30:02', '2026-07-19 03:50:42'),
(75, 21, 10, 'view', 21, NULL, '2026-07-19 05:06:11', NULL),
(246, 21, 23, 'view', 4, NULL, '2026-07-19 18:12:35', NULL),
(301, 21, NULL, 'book', NULL, NULL, NULL, NULL),
(332, 11, 15, 'view', 24, NULL, '2026-07-27 06:46:26', NULL),
(356, 11, 22, 'view', 11, NULL, '2026-07-27 06:49:00', NULL),
(500, 100, 10, 'view', 15, 89, '2026-02-17 13:51:25', '2026-02-07 13:33:07'),
(501, 100, 28, 'view', 7, 31, '2026-02-11 06:06:39', '2026-02-04 11:20:27'),
(502, 100, 5, 'view', 14, 118, '2026-03-25 13:52:25', '2026-03-17 02:40:55'),
(503, 100, 19, 'view', 5, 773, '2026-02-04 07:36:49', '2026-02-02 02:47:40'),
(504, 101, 31, 'view', 9, 208, '2026-01-30 12:01:52', '2026-01-20 04:30:06'),
(505, 102, 20, 'view', 3, 626, '2026-05-10 02:26:49', '2026-04-30 15:44:48'),
(506, 102, 18, 'view', 10, 788, '2025-10-04 14:58:59', '2025-09-25 11:10:40'),
(507, 103, 16, 'view', 8, 462, '2026-02-22 08:52:03', '2026-02-17 16:19:19'),
(508, 103, 29, 'view', 8, 472, '2026-07-08 01:38:53', '2026-07-04 02:56:55'),
(509, 103, 13, 'view', 14, 102, '2025-11-10 12:44:37', '2025-11-02 01:43:58'),
(510, 104, 18, 'view', 1, 471, '2026-06-05 11:00:05', '2026-05-28 05:17:15'),
(511, 104, 22, 'view', 12, 458, '2025-12-15 16:48:39', '2025-12-13 11:38:58'),
(512, 104, 30, 'view', 5, 406, '2025-12-30 10:58:38', '2025-12-30 10:19:21'),
(513, 104, 20, 'view', 2, 581, '2026-01-11 09:31:46', '2026-01-01 04:36:05'),
(514, 104, 13, 'view', 10, 689, '2026-05-16 11:34:41', '2026-05-06 12:23:38'),
(515, 105, 13, 'view', 14, 140, '2025-10-31 02:56:42', '2025-10-25 16:38:01'),
(516, 105, 21, 'view', 6, 326, '2026-04-26 06:36:49', '2026-04-19 06:29:08'),
(517, 105, 27, 'view', 3, 89, '2026-02-26 03:47:49', '2026-02-18 16:17:42'),
(518, 105, 22, 'view', 6, 794, '2026-05-21 12:24:10', '2026-05-19 06:01:28'),
(519, 105, 30, 'view', 1, 430, '2026-01-22 07:43:39', '2026-01-18 14:29:34'),
(520, 105, 17, 'view', 4, 326, '2025-11-29 06:38:43', '2025-11-20 14:44:49'),
(521, 106, 27, 'view', 3, 846, '2026-04-16 05:10:16', '2026-04-16 16:10:23'),
(522, 106, 31, 'view', 9, 115, '2025-11-05 03:33:05', '2025-11-03 08:43:57'),
(523, 106, 28, 'view', 9, 160, '2025-12-16 03:13:14', '2025-12-09 12:14:01'),
(524, 107, 10, 'view', 7, 93, '2026-01-17 07:16:20', '2026-01-16 11:05:09'),
(525, 107, 27, 'view', 3, 49, '2026-03-03 15:59:53', '2026-03-01 15:43:39'),
(526, 107, 26, 'view', 1, 91, '2026-07-18 08:28:53', '2026-07-16 16:42:07'),
(527, 107, 15, 'view', 13, 305, '2026-03-28 09:22:03', '2026-03-25 13:55:50'),
(528, 108, 29, 'view', 10, 558, '2026-07-19 16:51:15', '2026-07-17 09:42:00'),
(529, 108, 13, 'view', 10, 371, '2026-03-26 13:26:42', '2026-03-16 02:48:23'),
(530, 108, 15, 'view', 2, 548, '2025-10-22 17:05:32', '2025-10-14 00:39:55'),
(531, 108, 28, 'view', 8, 54, '2026-01-10 11:31:47', '2026-01-10 12:05:04'),
(532, 108, 19, 'view', 6, 256, '2026-06-04 10:23:02', '2026-05-30 11:07:41'),
(533, 109, 29, 'view', 11, 196, '2026-05-19 03:00:49', '2026-05-12 01:33:12'),
(534, 109, 30, 'view', 1, 820, '2026-04-16 01:35:59', '2026-04-12 16:40:52'),
(535, 110, 30, 'view', 4, 302, '2026-01-26 02:10:41', '2026-01-21 08:22:51'),
(536, 110, 20, 'view', 6, 457, '2026-01-04 15:12:24', '2025-12-30 05:46:44'),
(537, 110, 10, 'view', 8, 386, '2025-11-04 09:06:05', '2025-10-29 02:42:38'),
(538, 110, 27, 'view', 14, 194, '2025-10-21 09:35:06', '2025-10-20 10:57:18'),
(539, 110, 22, 'view', 5, 466, '2025-12-21 05:59:28', '2025-12-16 14:17:46'),
(540, 111, 28, 'view', 2, 697, '2026-01-01 11:47:51', '2025-12-22 05:16:09'),
(541, 111, 20, 'view', 14, 632, '2025-12-15 01:23:04', '2025-12-12 11:38:24'),
(542, 111, 10, 'view', 10, 43, '2026-05-10 14:38:23', '2026-05-03 02:51:08'),
(543, 112, 26, 'view', 4, 126, '2026-07-14 05:46:33', '2026-07-08 03:31:49'),
(544, 112, 27, 'view', 5, 730, '2025-12-11 06:54:18', '2025-12-04 06:22:08'),
(545, 112, 22, 'view', 14, 85, '2025-12-08 06:12:45', '2025-12-01 03:06:43'),
(546, 112, 30, 'view', 14, 337, '2026-01-31 02:50:34', '2026-01-27 10:09:10'),
(547, 112, 16, 'view', 12, 736, '2026-04-29 11:47:14', '2026-04-28 07:05:08'),
(548, 113, 30, 'view', 6, 577, '2025-10-07 11:44:51', '2025-09-29 04:54:56'),
(549, 113, 5, 'view', 2, 77, '2026-02-19 13:00:46', '2026-02-12 16:41:49'),
(550, 114, 13, 'view', 2, 470, '2025-12-01 16:37:08', '2025-11-23 06:04:08'),
(551, 114, 16, 'view', 7, 524, '2026-06-29 03:48:09', '2026-06-25 05:25:20'),
(552, 114, 22, 'view', 15, 736, '2026-04-03 11:48:57', '2026-03-30 02:31:12'),
(553, 114, 30, 'view', 11, 574, '2026-03-09 04:55:19', '2026-02-28 02:47:41'),
(554, 115, 16, 'view', 11, 649, '2026-02-05 02:07:55', '2026-02-05 02:17:41'),
(555, 115, 17, 'view', 13, 600, '2026-03-14 07:04:36', '2026-03-08 00:46:56'),
(556, 116, 32, 'view', 13, 711, '2026-01-01 09:44:04', '2026-01-01 05:43:26'),
(557, 116, 21, 'view', 8, 485, '2026-04-14 10:53:09', '2026-04-09 11:01:55'),
(558, 116, 31, 'view', 6, 418, '2026-05-22 11:47:35', '2026-05-21 10:30:29'),
(559, 116, 19, 'view', 2, 283, '2025-12-09 07:24:06', '2025-12-09 09:39:55'),
(560, 116, 15, 'view', 10, 438, '2026-03-22 06:07:20', '2026-03-13 10:27:48'),
(561, 116, 27, 'view', 3, 520, '2025-11-06 14:46:56', '2025-11-02 15:16:05'),
(562, 117, 31, 'view', 10, 371, '2026-07-03 01:33:31', '2026-06-24 15:33:34'),
(563, 117, 29, 'view', 1, 877, '2026-06-02 13:23:56', '2026-05-29 12:03:25'),
(564, 117, 19, 'view', 6, 56, '2026-01-03 01:51:35', '2025-12-31 11:50:18'),
(565, 117, 15, 'view', 2, 405, '2025-11-11 15:03:35', '2025-11-07 03:23:06'),
(566, 118, 26, 'view', 3, 213, '2025-11-09 12:47:02', '2025-11-09 01:23:45'),
(567, 118, 22, 'view', 6, 833, '2025-11-27 16:44:09', '2025-11-18 16:23:20'),
(568, 118, 32, 'view', 15, 637, '2026-02-14 05:40:39', '2026-02-10 10:47:53'),
(569, 118, 27, 'view', 9, 555, '2025-11-19 09:45:52', '2025-11-19 11:36:43'),
(570, 119, 28, 'view', 10, 325, '2026-07-14 15:31:41', '2026-07-05 08:01:03'),
(571, 120, 30, 'view', 11, 258, '2026-07-11 03:27:01', '2026-07-04 10:41:09'),
(572, 120, 17, 'view', 7, 717, '2026-04-14 13:47:49', '2026-04-05 16:10:54'),
(573, 120, 27, 'view', 12, 754, '2026-06-26 04:48:13', '2026-06-18 10:57:30'),
(574, 120, 13, 'view', 9, 395, '2026-02-17 05:44:58', '2026-02-09 10:49:22'),
(575, 120, 28, 'view', 11, 800, '2026-03-14 15:27:15', '2026-03-10 09:29:19'),
(576, 121, 18, 'view', 8, 367, '2025-10-14 08:33:07', '2025-10-05 13:12:25'),
(577, 121, 30, 'view', 14, 363, '2026-05-14 09:17:18', '2026-05-13 12:14:28'),
(578, 121, 22, 'view', 11, 272, '2025-11-24 06:27:52', '2025-11-16 09:14:35'),
(579, 122, 20, 'view', 4, 258, '2026-07-02 16:29:40', '2026-06-29 01:21:26'),
(580, 122, 16, 'view', 6, 744, '2025-11-28 03:58:49', '2025-11-26 00:50:59'),
(581, 122, 15, 'view', 3, 426, '2025-11-26 15:56:12', '2025-11-22 10:33:41'),
(582, 122, 21, 'view', 1, 797, '2026-06-12 07:49:47', '2026-06-12 05:41:56'),
(583, 122, 26, 'view', 14, 190, '2026-07-09 13:05:31', '2026-07-07 10:11:02'),
(584, 122, 29, 'view', 1, 315, '2025-10-10 04:14:21', '2025-10-06 14:56:34'),
(585, 123, 30, 'view', 13, 125, '2026-02-08 06:01:29', '2026-01-29 09:00:11'),
(586, 123, 16, 'view', 1, 764, '2026-02-05 09:51:43', '2026-02-02 05:54:54'),
(587, 123, 29, 'view', 11, 425, '2025-12-21 16:35:05', '2025-12-15 03:26:08'),
(588, 123, 31, 'view', 8, 341, '2026-03-22 00:31:24', '2026-03-19 15:03:17'),
(589, 123, 15, 'view', 6, 319, '2025-10-02 00:31:41', '2025-09-27 07:18:42'),
(590, 124, 29, 'view', 5, 173, '2026-01-01 17:14:57', '2025-12-28 03:55:58'),
(591, 125, 26, 'view', 3, 499, '2026-05-10 15:02:38', '2026-05-05 13:59:35'),
(592, 125, 19, 'view', 15, 492, '2025-10-25 07:03:15', '2025-10-15 02:48:28'),
(593, 125, 31, 'view', 9, 730, '2026-01-23 03:13:36', '2026-01-22 02:08:35'),
(594, 126, 18, 'view', 9, 462, '2026-05-29 07:00:37', '2026-05-22 03:12:32'),
(595, 126, 16, 'view', 8, 838, '2026-06-29 14:23:32', '2026-06-23 14:51:03'),
(596, 126, 17, 'view', 9, 483, '2026-02-20 00:40:16', '2026-02-20 06:52:04'),
(597, 126, 30, 'view', 1, 443, '2026-02-01 02:49:03', '2026-01-31 15:17:18'),
(598, 126, 28, 'view', 7, 194, '2026-05-19 13:38:57', '2026-05-13 15:10:58'),
(599, 127, 27, 'view', 14, 366, '2026-05-28 05:49:25', '2026-05-20 05:01:14'),
(600, 127, 13, 'view', 14, 13, '2026-07-16 09:44:43', '2026-07-08 13:49:24'),
(601, 127, 16, 'view', 14, 245, '2026-03-23 14:37:09', '2026-03-19 07:14:56'),
(602, 127, 22, 'view', 12, 793, '2026-05-31 02:06:42', '2026-05-25 00:30:13'),
(603, 128, 15, 'view', 10, 44, '2025-12-11 01:30:47', '2025-12-11 12:43:14'),
(604, 129, 18, 'view', 4, 845, '2025-10-06 10:51:38', '2025-09-26 10:30:19'),
(605, 129, 10, 'view', 15, 156, '2025-11-03 07:41:19', '2025-10-30 01:50:37'),
(606, 129, 16, 'view', 15, 762, '2026-04-29 13:50:31', '2026-04-29 11:56:42'),
(607, 129, 20, 'view', 7, 814, '2025-11-01 10:59:26', '2025-10-26 04:34:24'),
(608, 129, 30, 'view', 3, 782, '2025-10-25 15:30:54', '2025-10-22 10:09:45'),
(900, 200, 28, 'view', 2, 753, '2026-02-04 12:30:53', '2026-01-30 14:08:36'),
(901, 200, 31, 'view', 11, 830, '2025-10-13 01:35:33', '2025-10-11 11:30:55'),
(902, 200, 10, 'view', 14, 33, '2026-01-22 03:48:11', '2026-01-21 10:42:12'),
(903, 200, 18, 'view', 17, 52, '2026-04-03 04:41:25', '2026-03-22 14:55:02'),
(904, 201, 10, 'view', 18, 83, '2025-09-12 03:31:07', '2025-09-04 00:42:15'),
(905, 201, 32, 'view', 2, 598, '2026-05-31 09:37:41', '2026-05-29 03:18:38'),
(906, 201, 20, 'view', 17, 559, '2026-06-14 14:52:34', '2026-06-12 14:22:32'),
(907, 201, 29, 'view', 5, 611, '2025-12-31 09:32:15', '2025-12-20 03:02:34'),
(908, 201, 22, 'view', 10, 940, '2025-09-18 07:56:24', '2025-09-15 11:44:57'),
(909, 201, 21, 'view', 18, 631, '2025-09-17 15:45:52', '2025-09-13 00:30:21'),
(910, 201, 16, 'view', 8, 396, '2025-11-07 12:52:25', '2025-11-07 11:25:55'),
(911, 202, 22, 'view', 16, 562, '2026-03-04 06:33:03', '2026-02-20 00:25:35'),
(912, 202, 32, 'view', 3, 722, '2025-12-14 01:48:24', '2025-12-07 12:02:48'),
(913, 203, 31, 'view', 8, 326, '2025-12-26 10:57:22', '2025-12-24 06:54:39'),
(914, 204, 20, 'view', 9, 270, '2025-12-28 03:15:26', '2025-12-16 03:46:25'),
(915, 204, 31, 'view', 5, 865, '2026-03-06 03:39:54', '2026-02-27 14:33:46'),
(916, 204, 15, 'view', 12, 609, '2026-01-28 12:48:35', '2026-01-19 12:56:20'),
(917, 204, 26, 'view', 1, 1033, '2026-01-14 14:34:11', '2026-01-06 10:06:09'),
(918, 204, 28, 'view', 14, 782, '2025-10-03 07:20:52', '2025-09-28 11:08:38'),
(919, 204, 29, 'view', 8, 677, '2026-04-14 14:12:58', '2026-04-14 00:18:16'),
(920, 204, 19, 'view', 16, 624, '2025-10-26 09:49:39', '2025-10-20 17:07:33'),
(921, 205, 28, 'view', 12, 937, '2026-07-22 02:48:14', '2026-07-21 13:38:32'),
(922, 205, 27, 'view', 13, 325, '2026-04-22 13:46:25', '2026-04-15 10:59:33'),
(923, 205, 29, 'view', 3, 359, '2026-01-23 10:38:04', '2026-01-19 16:26:07'),
(924, 205, 17, 'view', 10, 713, '2025-11-09 13:55:10', '2025-11-01 10:07:32'),
(925, 205, 5, 'view', 7, 1044, '2026-04-22 13:26:03', '2026-04-12 03:37:36'),
(926, 205, 21, 'view', 2, 852, '2026-07-22 00:34:45', '2026-07-11 01:13:19'),
(927, 206, 15, 'view', 6, 1029, '2025-10-17 09:10:41', '2025-10-09 16:24:36'),
(928, 206, 5, 'view', 7, 851, '2025-09-22 03:24:10', '2025-09-14 16:21:01'),
(929, 206, 31, 'view', 4, 165, '2026-05-01 16:46:52', '2026-04-24 14:06:51'),
(930, 206, 32, 'view', 2, 35, '2025-08-11 10:24:45', '2025-08-08 11:32:10'),
(931, 207, 20, 'view', 4, 139, '2026-01-30 06:43:39', '2026-01-24 00:18:14'),
(932, 208, 10, 'view', 8, 520, '2026-04-04 01:25:59', '2026-03-26 05:35:00'),
(933, 208, 29, 'view', 15, 631, '2025-12-25 09:11:31', '2025-12-24 07:58:24'),
(934, 208, 32, 'view', 8, 856, '2026-02-19 13:11:45', '2026-02-12 01:05:55'),
(935, 208, 21, 'view', 8, 189, '2026-04-30 05:37:24', '2026-04-28 01:11:18'),
(936, 209, 32, 'view', 18, 799, '2026-02-06 12:56:04', '2026-02-05 14:07:58'),
(937, 209, 26, 'view', 12, 511, '2026-01-10 06:44:18', '2026-01-05 07:42:02'),
(938, 209, 15, 'view', 9, 61, '2026-02-03 04:30:45', '2026-02-01 02:27:17'),
(939, 209, 17, 'view', 18, 271, '2025-10-16 14:44:53', '2025-10-04 07:25:23'),
(940, 210, 18, 'view', 7, 618, '2025-11-26 16:28:14', '2025-11-19 05:00:16'),
(941, 210, 27, 'view', 15, 763, '2025-10-27 07:40:38', '2025-10-19 06:23:55'),
(942, 210, 31, 'view', 4, 1060, '2026-06-11 09:02:49', '2026-05-30 12:16:42'),
(943, 211, 16, 'view', 6, 484, '2026-02-13 06:57:57', '2026-02-12 02:50:58'),
(944, 211, 21, 'view', 12, 1034, '2026-02-25 06:19:45', '2026-02-21 02:29:18'),
(945, 211, 5, 'view', 5, 827, '2026-03-05 11:40:54', '2026-02-26 05:14:17'),
(946, 211, 18, 'view', 6, 70, '2026-01-21 12:12:26', '2026-01-21 14:30:54'),
(947, 211, 26, 'view', 13, 731, '2025-09-09 03:26:18', '2025-09-08 09:13:38'),
(948, 211, 10, 'view', 8, 92, '2026-01-01 01:53:10', '2025-12-26 07:03:19'),
(949, 212, 27, 'view', 18, 646, '2025-09-08 05:51:53', '2025-09-05 16:00:33'),
(950, 212, 10, 'view', 9, 900, '2025-08-18 12:14:00', '2025-08-17 10:12:02'),
(951, 213, 10, 'view', 12, 186, '2025-12-26 13:02:39', '2025-12-23 08:48:05'),
(952, 213, 19, 'view', 12, 878, '2025-12-13 10:59:32', '2025-12-02 14:47:03'),
(953, 213, 15, 'view', 7, 887, '2025-08-17 17:09:59', '2025-08-05 04:46:48'),
(954, 213, 5, 'view', 7, 99, '2025-10-14 08:26:34', '2025-10-12 07:49:16'),
(955, 213, 27, 'view', 8, 131, '2026-05-02 11:37:26', '2026-05-01 06:55:19'),
(956, 213, 17, 'view', 5, 289, '2025-11-20 15:30:45', '2025-11-17 00:47:44'),
(957, 213, 30, 'view', 15, 282, '2025-09-02 11:59:19', '2025-08-31 04:52:36'),
(958, 214, 22, 'view', 18, 879, '2026-05-02 04:53:29', '2026-04-20 13:08:13'),
(959, 214, 15, 'view', 4, 602, '2026-07-21 11:46:13', '2026-07-21 02:12:17'),
(960, 215, 18, 'view', 15, 241, '2026-05-06 10:43:29', '2026-04-27 11:33:10'),
(961, 215, 15, 'view', 18, 157, '2026-07-04 00:44:48', '2026-06-27 03:02:45'),
(962, 215, 21, 'view', 11, 551, '2026-06-02 15:42:31', '2026-05-30 10:15:22'),
(963, 216, 21, 'view', 9, 513, '2026-06-17 05:02:01', '2026-06-17 13:08:09'),
(964, 217, 26, 'view', 4, 645, '2025-09-15 10:39:11', '2025-09-05 11:35:14'),
(965, 217, 17, 'view', 12, 289, '2025-10-18 12:08:53', '2025-10-14 07:18:02'),
(966, 217, 31, 'view', 4, 835, '2026-07-02 06:46:27', '2026-06-25 05:34:38'),
(967, 218, 13, 'view', 15, 832, '2026-06-12 02:09:28', '2026-06-05 06:28:46'),
(968, 218, 16, 'view', 12, 15, '2026-07-11 16:42:09', '2026-07-07 02:57:03'),
(969, 218, 19, 'view', 17, 872, '2026-02-04 02:43:00', '2026-01-25 06:12:46'),
(970, 218, 32, 'view', 6, 785, '2026-02-26 00:43:51', '2026-02-17 11:51:12'),
(971, 218, 29, 'view', 16, 184, '2025-10-23 10:48:29', '2025-10-17 04:40:38'),
(972, 219, 13, 'view', 10, 872, '2026-01-20 15:57:41', '2026-01-18 10:10:21'),
(973, 219, 10, 'view', 17, 67, '2026-04-22 07:58:47', '2026-04-15 02:24:42'),
(974, 219, 22, 'view', 12, 862, '2026-01-24 16:30:36', '2026-01-17 12:31:07'),
(975, 219, 21, 'view', 8, 379, '2026-04-15 03:29:55', '2026-04-11 03:27:33'),
(976, 219, 30, 'view', 9, 1012, '2026-04-02 14:29:34', '2026-03-24 04:02:32'),
(977, 220, 13, 'view', 5, 1040, '2025-10-19 17:00:53', '2025-10-07 03:55:46'),
(978, 220, 28, 'view', 17, 219, '2025-12-04 12:49:10', '2025-12-01 16:04:05'),
(979, 220, 32, 'view', 5, 774, '2025-09-14 01:40:15', '2025-09-14 11:17:00'),
(980, 220, 27, 'view', 7, 951, '2026-02-24 04:00:08', '2026-02-18 02:54:55'),
(981, 220, 19, 'view', 7, 244, '2026-01-27 05:38:47', '2026-01-22 01:07:16'),
(982, 221, 19, 'view', 12, 1060, '2025-11-01 12:01:31', '2025-11-01 11:21:22'),
(983, 222, 22, 'view', 8, 531, '2026-01-27 06:59:28', '2026-01-27 14:22:50'),
(984, 222, 15, 'view', 1, 1009, '2026-06-01 03:06:16', '2026-05-30 04:50:59'),
(985, 222, 10, 'view', 10, 789, '2026-05-15 08:49:44', '2026-05-03 08:43:00'),
(986, 222, 29, 'view', 1, 711, '2026-05-11 15:47:30', '2026-05-11 01:19:11'),
(987, 222, 32, 'view', 13, 984, '2026-05-07 14:40:14', '2026-04-28 16:19:23'),
(988, 223, 31, 'view', 5, 99, '2026-04-10 06:07:23', '2026-03-30 14:36:36'),
(989, 223, 18, 'view', 15, 804, '2026-01-27 10:15:21', '2026-01-18 15:36:14'),
(990, 223, 21, 'view', 1, 519, '2025-12-04 01:55:09', '2025-11-23 04:32:24'),
(991, 224, 13, 'view', 12, 1091, '2025-10-01 04:59:02', '2025-09-23 04:10:12'),
(992, 224, 31, 'view', 14, 212, '2026-01-23 10:05:50', '2026-01-20 04:58:04'),
(993, 224, 20, 'view', 10, 709, '2026-01-23 17:09:40', '2026-01-20 12:10:35'),
(994, 225, 27, 'view', 11, 996, '2025-11-12 12:12:15', '2025-10-31 07:37:09'),
(995, 225, 22, 'view', 5, 430, '2026-07-24 14:40:28', '2026-07-18 10:14:10'),
(996, 225, 10, 'view', 3, 304, '2026-02-23 09:31:46', '2026-02-14 10:19:58'),
(997, 225, 26, 'view', 7, 173, '2025-10-01 05:34:37', '2025-09-26 14:37:49'),
(998, 225, 17, 'view', 14, 148, '2025-11-21 11:12:11', '2025-11-17 08:49:01'),
(999, 225, 31, 'view', 6, 558, '2026-03-28 00:28:03', '2026-03-22 14:27:57'),
(1000, 226, 21, 'view', 2, 274, '2025-09-23 01:20:04', '2025-09-11 11:01:08'),
(1001, 226, 31, 'view', 1, 395, '2026-03-11 00:55:20', '2026-03-11 06:35:20'),
(1002, 226, 15, 'view', 1, 1005, '2026-01-01 10:26:03', '2025-12-26 01:20:40'),
(1003, 226, 30, 'view', 11, 1022, '2025-09-24 12:31:29', '2025-09-24 01:14:20'),
(1004, 226, 29, 'view', 11, 124, '2025-12-27 10:25:05', '2025-12-27 04:28:09'),
(1005, 227, 13, 'view', 18, 324, '2025-08-25 10:29:47', '2025-08-16 09:07:45'),
(1006, 227, 26, 'view', 16, 74, '2025-08-30 09:56:49', '2025-08-22 14:50:17'),
(1007, 227, 31, 'view', 12, 1081, '2025-10-29 08:23:16', '2025-10-29 15:21:41'),
(1008, 227, 18, 'view', 12, 318, '2025-09-09 07:40:48', '2025-09-08 00:54:08'),
(1009, 227, 17, 'view', 4, 133, '2025-10-22 16:28:35', '2025-10-10 05:31:38'),
(1010, 228, 16, 'view', 17, 69, '2026-01-29 07:43:55', '2026-01-22 06:55:58'),
(1011, 228, 17, 'view', 12, 806, '2025-12-04 06:35:50', '2025-12-04 03:57:46'),
(1012, 228, 31, 'view', 1, 144, '2025-08-31 12:58:55', '2025-08-26 01:29:36'),
(1013, 229, 28, 'view', 9, 52, '2026-03-15 13:30:14', '2026-03-10 06:35:48'),
(1014, 229, 27, 'view', 14, 580, '2026-02-25 15:28:36', '2026-02-13 05:45:55'),
(1015, 229, 19, 'view', 9, 289, '2026-02-24 09:20:21', '2026-02-24 16:10:57'),
(1016, 229, 5, 'view', 8, 340, '2026-02-14 14:28:37', '2026-02-14 07:09:56'),
(1017, 230, 26, 'view', 10, 60, '2026-05-31 05:13:00', '2026-05-29 09:24:32'),
(1018, 230, 10, 'view', 12, 209, '2026-05-02 14:58:25', '2026-05-01 13:36:41'),
(1019, 230, 29, 'view', 13, 697, '2026-07-11 07:27:50', '2026-07-01 00:17:08'),
(1020, 230, 13, 'view', 17, 484, '2025-10-06 13:59:06', '2025-09-25 00:18:57'),
(1021, 230, 18, 'view', 11, 142, '2026-06-01 03:46:08', '2026-05-24 13:15:11'),
(1022, 230, 30, 'view', 8, 312, '2025-09-06 16:22:33', '2025-09-01 16:13:04'),
(1023, 231, 18, 'view', 9, 372, '2026-07-20 08:32:04', '2026-07-20 06:47:03'),
(1024, 231, 19, 'view', 14, 752, '2026-03-13 00:35:44', '2026-03-13 14:49:18'),
(1025, 231, 13, 'view', 18, 687, '2025-12-29 08:40:27', '2025-12-24 13:39:09'),
(1026, 232, 27, 'view', 1, 499, '2025-09-19 17:14:16', '2025-09-08 12:30:52'),
(1027, 232, 28, 'view', 7, 247, '2026-06-13 02:13:45', '2026-06-13 12:59:35'),
(1028, 232, 16, 'view', 11, 916, '2025-10-19 10:44:36', '2025-10-19 16:02:41'),
(1029, 232, 29, 'view', 16, 1054, '2026-02-02 12:30:52', '2026-01-23 12:37:45'),
(1030, 233, 27, 'view', 17, 555, '2025-09-17 10:19:40', '2025-09-05 08:14:39'),
(1031, 234, 20, 'view', 16, 463, '2026-05-16 03:14:48', '2026-05-08 11:48:13'),
(1032, 234, 32, 'view', 17, 356, '2026-01-21 07:58:11', '2026-01-19 14:26:40'),
(1033, 234, 30, 'view', 2, 669, '2026-01-13 12:08:55', '2026-01-07 03:41:09'),
(1034, 234, 28, 'view', 9, 778, '2026-06-05 11:37:42', '2026-05-24 16:48:19'),
(1035, 234, 26, 'view', 15, 190, '2026-03-09 12:33:28', '2026-02-26 03:43:40'),
(1036, 234, 17, 'view', 16, 367, '2025-11-05 04:15:43', '2025-11-03 11:46:33'),
(1037, 234, 31, 'view', 8, 769, '2025-11-02 11:06:24', '2025-10-29 00:50:12');

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_old`
--

CREATE TABLE `user_activity_old` (
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
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blog_id` (`blog_id`);

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
  ADD KEY `idx_package_id` (`package_id`),
  ADD KEY `idx_user_id` (`user_id`);

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
  ADD UNIQUE KEY `uniq_user_package_click` (`user_id`,`package_id`),
  ADD KEY `idx_package_id` (`package_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trip_slug` (`slug`);

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
  ADD KEY `fk_review_user` (`user_id`),
  ADD KEY `idx_trip_status` (`trip_id`,`status`);

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
  ADD UNIQUE KEY `uniq_user_package` (`user_id`,`package_id`);

--
-- Indexes for table `user_activity_old`
--
ALTER TABLE `user_activity_old`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_activity_package` (`package_id`),
  ADD KEY `idx_user_id` (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `blog_comments`
--
ALTER TABLE `blog_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `bus_inquiries`
--
ALTER TABLE `bus_inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `chatbot_quiries`
--
ALTER TABLE `chatbot_quiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=214;

--
-- AUTO_INCREMENT for table `flights`
--
ALTER TABLE `flights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `gallery_albums`
--
ALTER TABLE `gallery_albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gallery_photos`
--
ALTER TABLE `gallery_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=335;

--
-- AUTO_INCREMENT for table `package_bookings`
--
ALTER TABLE `package_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=460;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `recmnd_clicks`
--
ALTER TABLE `recmnd_clicks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `tour_itineraries`
--
ALTER TABLE `tour_itineraries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=322;

--
-- AUTO_INCREMENT for table `trip_reviews`
--
ALTER TABLE `trip_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=790;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=235;

--
-- AUTO_INCREMENT for table `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1038;

--
-- AUTO_INCREMENT for table `user_activity_old`
--
ALTER TABLE `user_activity_old`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_fcm_tokens`
--
ALTER TABLE `admin_fcm_tokens`
  ADD CONSTRAINT `fk_admin_fcm_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `fk_blog_category` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD CONSTRAINT `fk_blog_comment` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `user_activity_old`
--
ALTER TABLE `user_activity_old`
  ADD CONSTRAINT `fk_activity_package` FOREIGN KEY (`package_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

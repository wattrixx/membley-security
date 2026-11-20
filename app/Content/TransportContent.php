<?php

namespace App\Content;

class TransportContent
{
    public const OVERVIEW = <<<'CONTENT'
Your Partner in Transport, Logistics and Supply Chain Solutions

## Moving Businesses Forward Through Smarter Logistics

At Membley Transport & Logistics Services, we understand that efficient transportation and logistics are essential to keeping businesses moving.

We offer flexible solutions for businesses, organisations and individuals, supporting the movement, storage, coordination and delivery of goods and people. From local transportation and distribution to freight coordination and cross-border logistics, we work to identify practical, reliable and cost-effective solutions for each client.

## Our Core Services

### Warehousing and Storage

Storage solutions for raw materials, commercial goods and finished products, including coordination of storage arrangements, stock handling and movement according to client requirements.

### Inventory Management and Order Fulfilment

Support for inventory tracking, order processing, picking, packing and dispatch coordination to help businesses manage their goods efficiently.

### Freight Forwarding and Customs Coordination

Coordination of domestic and international cargo movements, including shipment planning, cargo bookings, documentation and customs clearance arrangements through appropriate service providers.

### Third-Party Logistics (3PL)

Integrated support through the coordination of transportation, warehousing, distribution and fulfilment services to help businesses streamline their supply chains.

### Reverse Logistics

Coordination of product returns, collection, redistribution, repair, recycling and disposal processes to help businesses manage goods after delivery.

### Specialised Logistics Solutions

Coordination for temperature-sensitive cargo, hazardous materials and heavy or oversized consignments, subject to appropriate equipment, permits and handling arrangements.

### Taxi and Passenger Transportation

Flexible transportation for individuals, businesses and organisations, including taxi services, scheduled journeys and corporate transport arrangements.

## Transportation Solutions

Different cargo and passenger requirements call for different approaches. We help identify suitable options based on distance, urgency, cargo type and budget.

### Road Transportation

Cargo and passenger transportation, including local distribution, long-distance journeys and last-mile delivery.

### Maritime Transportation

Sea freight coordination for bulk goods, containerised cargo and international shipments.

### Air Transportation

Air cargo solutions for urgent shipments, high-value goods and time-sensitive consignments.

### Rail Transportation

Coordination of rail freight for bulk cargo and long-distance transportation where suitable.

### Intermodal and Multimodal Transportation

Combining road, rail, sea and air transport to create practical end-to-end cargo movement solutions.

## Our Geographical Coverage

Based in Nairobi, along Thika Superhighway, we coordinate transport and logistics operations across Kenya and the wider East African region. Our intended coverage includes:

### Kenya

Nairobi and other major towns, commercial centres and regional destinations.

### Uganda

Cross-border transportation and logistics coordination.

### Tanzania

Regional freight and transport coordination.

### Rwanda

Cross-border cargo movement and logistics arrangements.

### Ethiopia

Regional transport and freight coordination, subject to applicable routes and arrangements.

## Availability and Service Arrangements

Transport and logistics requirements do not always follow a fixed schedule. We provide responsive support and flexible arrangements based on the nature and urgency of each request.

### Enquiries and Consultations

Contact us to discuss your transportation and logistics requirements.

### Flexible Scheduling

Services can be arranged in advance or discussed for urgent requirements, subject to availability.

### Planned Operations

Deliveries, collections, transportation and other activities are coordinated according to agreed timelines.

### Customised Arrangements

Each engagement is assessed against the destination, service requirements, cargo or passenger needs, applicable regulations and operational capacity.

### Regional Coordination

Cross-border and international movements are planned around relevant routes, documentation and regulatory requirements.

## How We Work

### 1. Understand Your Requirements

We discuss what needs to be transported, stored or coordinated, including destinations, timelines and any special requirements.

### 2. Develop a Practical Solution

We assess the requirements and identify suitable transportation methods, logistics arrangements and service options.

### 3. Agree on the Arrangements

We discuss the scope, costs, schedules and operational details before confirming the engagement.

### 4. Coordinate and Execute

Once agreed, we coordinate the necessary service providers, transportation and logistics activities.

### 5. Maintain Communication

We keep clients informed of progress, relevant updates and any changes that may affect the agreed arrangements.

## Before Every Engagement

We establish the scope of work, destinations, timelines, pricing and applicable terms with the client before confirming an engagement. Service availability and operational arrangements are verified on a case-by-case basis.

## Let's Move Your Business Forward

Whether you need transportation, freight coordination, storage solutions or a logistics partner for your business, contact our team to discuss your next requirement.
CONTENT;

    public const PAGES = [
        'transport-logistics' => [
            'title' => 'Transport & Logistics Services',
            'content' => self::OVERVIEW,
            'meta_title' => 'Transport & Logistics | Membley Security Group',
            'meta_description' => 'Explore transport, logistics, freight coordination and supply chain solutions from Membley.',
            'path' => 'transport-logistics',
            'image' => 'assets/images/transport-port.jpg',
            'alt' => 'Illustrative aerial view of shipping containers at a port',
        ],
        'transport-logistics-services' => [
            'title' => 'Transport & Logistics: Services',
            'content' => "Our services include warehousing and storage, inventory and order fulfilment, freight and customs coordination, third-party and reverse logistics, specialised logistics, and passenger transport.\n\nWe coordinate road, maritime, air, rail and multimodal transport as appropriate. Explore the overview for details, and contact us to discuss availability, routes and arrangements for your requirements.",
            'meta_title' => 'Logistics Services | Membley Security Group',
            'meta_description' => 'Explore transport, freight and logistics services from Membley.',
            'path' => 'transport-logistics/services',
            'image' => 'assets/images/transport-warehouse.jpg',
            'alt' => 'Illustrative view of goods stored in a warehouse',
        ],
        'transport-logistics-enquiries' => [
            'title' => 'Transport & Logistics: Enquiry Guide',
            'content' => "Contact us to discuss the type of goods or passenger transport support needed, the intended locations and your preferred timeframe. Services can be arranged in advance or discussed for urgent requirements, subject to availability.\n\nWe agree the scope, timelines, pricing and applicable terms before confirming an engagement. Do not send sensitive shipment or personal details until an appropriate communication channel has been agreed.",
            'meta_title' => 'Logistics Enquiries | Membley Security Group',
            'meta_description' => 'Find out how to start a general transport and logistics enquiry.',
            'path' => 'transport-logistics/enquiries',
            'image' => 'assets/images/transport-port.jpg',
            'alt' => 'Illustrative aerial view of shipping containers at a port',
        ],
    ];
}

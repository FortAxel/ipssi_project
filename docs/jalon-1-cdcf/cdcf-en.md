# Functional Specification Document
## Milestone 1 – Functional Project Presentation

---

## 1. Business Context

The project falls within the domain of digital applications for young audiences, more specifically in the reading and educational entertainment sector for children. Reading stories plays a fundamental role in developing imagination, language skills, and concentration in children. However, existing digital media are often poorly adapted to supervised use, too complex, or insufficiently structured to meet the specific needs of this audience.

The proposed application aims to address this issue by offering a platform dedicated to reading stories for children, based on existing illustrated narrative content. Each story is structured in successive pages, with each page corresponding to a paragraph of approximately thirty words accompanied by an illustration. This structure enables progressive reading, better suited to children's attention spans.

The application primarily targets parents, who play an intermediary and supervisory role. It provides a secure, user-friendly digital environment designed for regular story consultation by children.

---

## 2. Project Objectives

The main objective of the project is to design and develop a web application allowing the consultation of children's stories in a secure and structured environment.

The functional objectives of the project are as follows:

- Enable a user to create an account and authenticate securely.
- Provide access to a catalog of children's stories, illustrated and structured in pages.
- Allow page-by-page story reading, combining text and images.
- Offer a simple, smooth reading experience adapted to a young audience.
- Provide management features such as favorites and reading history.
- Include an administration interface allowing story management and moderation.
- Enhance the user experience by integrating an external text-to-speech service.

These objectives are defined in a realistic and achievable manner within the framework of an individual development project over a six-month period.

---

## 3. Functional Scope (Functional Requirements)

### Feature 1: User Management

The application enables user management with the following functionalities:
- User account creation (parent)
- Authentication and logout
- User profile management
- Role assignment (standard user, administrator)

### Feature 2: Story Catalog

Authenticated users can:
- Browse the list of available stories
- Access story details (title, description, cover illustration)
- Filter or sort stories according to simple criteria (e.g., favorites)

### Feature 3: Story Reading

The application allows:
- Page-by-page story reading
- Simultaneous display of text and associated illustration
- Simple navigation between pages (next/previous page)

### Feature 4: Favorites and History

Users can:
- Add or remove a story from their favorites
- View the history of stories already read

### Feature 5: Administration Interface

A user with administrator role can:
- Add, modify, or delete stories
- Manage pages associated with a story
- Ensure consistency and quality of proposed content

### Feature 6: Audio Reading (External API)

The application offers an audio reading feature for stories via an external text-to-speech service. This functionality allows listening to the text content of a page or complete story.

#### Out of Scope
Automatic generation of new stories using artificial intelligence is not part of the project scope.

---

## 4. Technical Requirements

### Architecture

The project is based on a **REST API** architecture, with clear separation between front-end and back-end:
- Back-end: API developed with the Symfony framework
- Front-end: web application developed with React and TypeScript

This choice is justified by the desire to offer a responsive and scalable user interface, suitable for general public use.

### Technical Components

- Back-end language: PHP 8+
- Back-end framework: Symfony
- Front-end: React 18 with TypeScript
- Database: MySQL
- Database administration tool: phpMyAdmin
- Containerization: Docker and Docker Compose
- Version control: Git

### Imposed Technical Constraints

- Use of a relational SQL database
- Use of Docker for the development environment
- Implementation of security best practices (authentication, access management)
- Maintainable code respecting development standards (PSR)

---

## 5. Project Constraints and Challenges

### Time Constraints

The project spans six months and is divided into several monthly milestones, of which this document constitutes the first. Each milestone validates a project stage before moving to the next.

### Regulatory Constraints

The application handles personal data (user accounts). It must therefore comply with general GDPR principles, particularly regarding data protection and access security.

### External Dependencies and Risks

The main identified risks are:
- Dependence on an external text-to-speech API, which may evolve or become unavailable
- Limited time to implement all planned functionalities
- The need to maintain a controlled functional scope

These risks will be mitigated through simple technical choices, feature prioritization, and controlled integration of external services.

---

## 6. Project Success Criteria

The project will be considered successful if:
- All main functionalities described in this document are implemented
- The application is functional, stable, and secure
- API response times remain acceptable for standard use
- The user interface is clear, intuitive, and tested
- The code is structured, documented, and maintainable

---

## 7. Conclusion

This functional specification document constitutes the project reference for all upcoming development phases. It defines the functional framework, objectives, constraints, and challenges of the application. Once validated, it will serve as the basis for the detailed design phase and technical implementation of the project.
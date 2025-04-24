// Import page components
import About from './pages/About.vue';
import EducationAndScholarship from './pages/services/EducationAndScholarship.vue';
import TrainingAndProfessionalDevelopment from './pages/services/TrainingAndProfessionalDevelopment.vue';
import AiAndAdvancedTechnologies from './pages/services/AiAndAdvancedTechnologies.vue';
import EgamingAndEsport from './pages/services/EgamingAndEsport.vue';
import ArtsAndEntertainment from './pages/services/ArtsAndEntertainment.vue';

const routes = [
    // Home route removed - handled by Laravel Blade template
    {
        path: '/about',
        name: 'about',
        component: About
    },
    {
        path: '/services/education-and-scholarship',
        name: 'services.education',
        component: EducationAndScholarship
    },
    {
        path: '/services/training-and-professional-development',
        name: 'services.training',
        component: TrainingAndProfessionalDevelopment
    },
    {
        path: '/services/ai-and-advanced-technologies',
        name: 'services.ai',
        component: AiAndAdvancedTechnologies
    },
    {
        path: '/services/egaming-and-esport',
        name: 'services.egaming',
        component: EgamingAndEsport
    },
    {
        path: '/services/arts-and-entertainment',
        name: 'services.arts',
        component: ArtsAndEntertainment
    },
];

export default routes; 